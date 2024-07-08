#include <ESP8266WiFi.h>       // Include the ESP8266 WiFi library
#include <ESP8266HTTPClient.h> // Include the ESP8266 HTTP client library
#include <DHT.h>               // Include the DHT sensor library
#include <NewPing.h>           // Include the NewPing library for HC-SR04 sonar sensor

#define DHTPIN D5              // Define the pin connected to the DHT22 sensor
#define DHTTYPE DHT22          // Define the type of DHT sensor (DHT22)
#define TRIGGER_PIN D6         // Define the pin for the sonar sensor trigger
#define ECHO_PIN D7            // Define the pin for the sonar sensor echo

DHT dht(DHTPIN, DHTTYPE);      // Initialize the DHT sensor
WiFiClient client;             // Create a WiFi client object
String URL = "http://192.168.29.34/env_project/index.php"; // Define the server URL
const char* ssid = "realme 8 Pro";     // Define the WiFi network SSID
const char* password = "bd702fbcc4";   // Define the WiFi network password

int temperature = 0;           // Initialize temperature variable
int humidity = 0;              // Initialize humidity variable
int distance = 0;              // Initialize distance variable

NewPing sonar(TRIGGER_PIN, ECHO_PIN);  // Initialize the sonar sensor with trigger and echo pins

void setup() {
  Serial.begin(115200);        // Start serial communication at 115200 baud rate
  dht.begin();                 // Initialize the DHT sensor
  connectWiFi();               // Connect to the WiFi network
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {  // Check if the WiFi is connected
    connectWiFi();           // Reconnect if WiFi is not connected
  }
  Load_DHT22_Data();         // Load data from the DHT22 sensor
  readSonarData();           // Load data from the sonar sensor

  String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&distance=" + String(distance);  // Create POST data string

  HTTPClient http;           // Create an HTTP client object
  http.begin(client, URL);   // Begin the HTTP connection
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Add the HTTP header for form data

  int httpCode = http.POST(postData);  // Send the POST request
  String payload = "";        // Initialize payload string

  if (httpCode > 0) {         // Check if the request was successful
    if (httpCode == HTTP_CODE_OK) {   // Check if the response code is OK
      payload = http.getString();     // Get the response payload
      Serial.println("Server response: " + payload);  // Print server response
    } else {
      Serial.printf("[HTTP] POST... code: %d\n", httpCode);  // Print the HTTP response code
    }
  } else {
    Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());  // Print the error if request failed
  }

  http.end();                 // End the HTTP connection

  Serial.print("URL : ");     // Print the server URL
  Serial.println(URL);
  Serial.print("Data: ");     // Print the POST data
  Serial.println(postData);
  Serial.print("httpCode: "); // Print the HTTP response code
  Serial.println(httpCode);
  Serial.println("--------------------------------------------------");

  delay(10000);               // Delay for 10 seconds before the next loop
}

void Load_DHT22_Data() {
  temperature = dht.readTemperature(); // Read temperature from DHT22 sensor
  humidity = dht.readHumidity();       // Read humidity from DHT22 sensor

  if (isnan(temperature) || isnan(humidity)) {  // Check if the readings are valid
    Serial.println("Failed to read from DHT sensor!");  // Print error message if readings are not valid
    temperature = 0;  // Set temperature to 0 if invalid
    humidity = 0;     // Set humidity to 0 if invalid
  }

  Serial.printf("Temperature: %d °C\n", temperature); // Print the temperature value
  Serial.printf("Humidity: %d %%\n", humidity);       // Print the humidity value
}

void readSonarData() {
  unsigned int uS = sonar.ping();  // Send a ping and get the echo time in microseconds
  distance = sonar.convert_cm(uS); // Convert the echo time to distance in centimeters

  Serial.print("Distance: ");      // Print the distance value
  Serial.print(distance);
  Serial.println(" cm");
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);       // Turn off WiFi to reset the connection
  delay(1000);               // Delay for 1 second
  WiFi.mode(WIFI_STA);       // Set WiFi mode to station
  WiFi.begin(ssid, password);  // Begin WiFi connection with SSID and password

  Serial.println("Connecting to WiFi");  // Print connection status
  while (WiFi.status() != WL_CONNECTED) { // Wait until connected to WiFi
    delay(500);               // Delay for 0.5 second
    Serial.print(".");        // Print a dot for connection progress
  }

  Serial.println("");         // Print a new line after connection
  Serial.print("Connected to "); // Print connection message
  Serial.println(ssid);       // Print the SSID
  Serial.print("IP address: "); // Print IP address message
  Serial.println(WiFi.localIP()); // Print the local IP address
}
