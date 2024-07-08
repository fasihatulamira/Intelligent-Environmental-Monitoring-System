# Environmental Monitoring System

## Setup Instructions

### Clone Repository:

```bash
git clone https://github.com/your-username/env_monitoring_system.git
cd env_monitoring_system
```

### Database Setup:
1. Ensure you have MySQL installed.
2. Create a new database named env_monitor.
   
### Database Configuration:
1. Open index.php and update the following variables with your MySQL credentials:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "env_monitor";
```
### Import Database Schema:
If you have an SQL file with schema:
```bash
mysql -u username -p env_monitor < schema.sql
```
### Run the Application:
Start a local PHP server:
```bash
php -S localhost:8000
```
Open your web browser and navigate to http://localhost:8000.

## Usage
The system will display real-time environmental data from your sensors.
Charts and summary tables provide insights into temperature, humidity, and distance metrics.
Notifications alert users based on predefined thresholds (e.g., high temperature, low humidity).
```css
In this setup, make sure to replace `your-username` with your actual GitHub username and adjust any other paths or configurations according to your project structure and requirements. This outline provides a clear path for anyone setting up your system to follow.
```
