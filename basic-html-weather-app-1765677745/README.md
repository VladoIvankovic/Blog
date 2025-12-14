# Basic HTML Weather App

A simple and clean weather application built with HTML, CSS, and JavaScript that displays current weather information for any city.

## Features

- 🌤️ Current weather display
- 🔍 City search functionality
- 📱 Responsive design
- 🎨 Clean and modern UI
- ⌨️ Enter key support for search
- 📊 Detailed weather information (temperature, humidity, wind speed)

## Files Structure

```
weather-app/
├── index.html      # Main HTML file
├── style.css       # Styling and responsive design
├── script.js       # JavaScript functionality
└── README.md       # This file
```

## Setup Instructions

### Option 1: Using Mock Data (No API Key Required)
1. Clone or download the project files
2. Open `index.html` in your web browser
3. The app will work with mock data for demonstration

### Option 2: Using Real Weather Data
1. Get a free API key from [OpenWeatherMap](https://openweathermap.org/api)
2. Open `script.js` and replace `'your_api_key_here'` with your actual API key
3. Comment out the mock data section (lines 21-40)
4. Uncomment the real API call section (lines 47-54)
5. Open `index.html` in your web browser

## How to Use

1. Enter a city name in the search input
2. Click "Get Weather" button or press Enter
3. View the current weather information including:
   - City name
   - Current temperature
   - Weather description
   - Feels like temperature
   - Humidity percentage
   - Wind speed

## Technologies Used

- **HTML5** - Structure and markup
- **CSS3** - Styling, animations, and responsive design
- **JavaScript (ES6+)** - Functionality and API integration
- **OpenWeatherMap API** - Weather data source

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Customization

You can easily customize the app by:
- Modifying colors in `style.css`
- Adding more weather parameters
- Changing the default city
- Adding weather icons
- Implementing geolocation

## License

This project is open source and available under the MIT License.

## Demo

The app includes mock data so you can test it immediately without an API key. Just open `index.html` in your browser!