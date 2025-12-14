const API_KEY = 'your_api_key_here'; // Replace with your OpenWeatherMap API key
const API_URL = 'https://api.openweathermap.org/data/2.5/weather';

// Mock data for demonstration (remove when using real API)
const mockWeatherData = {
    name: "London",
    main: {
        temp: 18,
        feels_like: 16,
        humidity: 65
    },
    weather: [{
        description: "partly cloudy"
    }],
    wind: {
        speed: 5.2
    }
};

async function getWeather() {
    const cityInput = document.getElementById('cityInput');
    const city = cityInput.value.trim();
    
    if (!city) {
        showError('Please enter a city name');
        return;
    }
    
    try {
        hideError();
        hideWeatherResult();
        
        // For demonstration purposes, we'll use mock data
        // Uncomment the following lines and comment the mock section when using real API
        
        /*
        const response = await fetch(`${API_URL}?q=${city}&appid=${API_KEY}&units=metric`);
        
        if (!response.ok) {
            throw new Error('City not found');
        }
        
        const weatherData = await response.json();
        */
        
        // Mock API call delay
        await new Promise(resolve => setTimeout(resolve, 1000));
        const weatherData = mockWeatherData;
        
        displayWeather(weatherData);
        
    } catch (error) {
        showError('City not found. Please check the spelling and try again.');
    }
}

function displayWeather(data) {
    const cityName = document.getElementById('cityName');
    const temperature = document.getElementById('temperature');
    const description = document.getElementById('description');
    const feelsLike = document.getElementById('feelsLike');
    const humidity = document.getElementById('humidity');
    const windSpeed = document.getElementById('windSpeed');
    const weatherResult = document.getElementById('weatherResult');
    
    cityName.textContent = data.name;
    temperature.textContent = `${Math.round(data.main.temp)}°C`;
    description.textContent = data.weather[0].description;
    feelsLike.textContent = `${Math.round(data.main.feels_like)}°C`;
    humidity.textContent = `${data.main.humidity}%`;
    windSpeed.textContent = `${data.wind.speed} m/s`;
    
    weatherResult.classList.add('show');
}

function showError(message) {
    const errorElement = document.getElementById('errorMessage');
    errorElement.textContent = message;
    errorElement.classList.add('show');
}

function hideError() {
    const errorElement = document.getElementById('errorMessage');
    errorElement.classList.remove('show');
}

function hideWeatherResult() {
    const weatherResult = document.getElementById('weatherResult');
    weatherResult.classList.remove('show');
}

// Allow Enter key to trigger search
document.getElementById('cityInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        getWeather();
    }
});

// Load weather for default city on page load
window.addEventListener('load', function() {
    document.getElementById('cityInput').value = 'London';
    getWeather();
});