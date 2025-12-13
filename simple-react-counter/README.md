# Simple React Counter

A beautiful and interactive counter application built with React. This project demonstrates basic React concepts including state management, event handling, and component styling.

## Features

- ✨ Clean and modern UI design
- ➕ Increment counter
- ➖ Decrement counter  
- 🔄 Reset counter to zero
- 📱 Responsive design
- 🎨 Smooth animations and hover effects

## Getting Started

### Prerequisites

Make sure you have Node.js installed on your machine (version 14 or higher).

### Installation

1. Clone or download this project
2. Navigate to the project directory
3. Install dependencies:
   ```bash
   npm install
   ```

### Running the Application

Start the development server:
```bash
npm start
```

The application will open in your browser at `http://localhost:3000`.

## Project Structure

```
simple-react-counter/
├── public/
│   └── index.html          # HTML template
├── src/
│   ├── App.js             # Main component with counter logic
│   ├── App.css            # Styles for the application
│   └── index.js           # Entry point
├── package.json           # Project dependencies and scripts
└── README.md             # This file
```

## How It Works

The counter uses React's `useState` hook to manage the counter state. Three functions handle the counter operations:

- `increment()` - Adds 1 to the counter
- `decrement()` - Subtracts 1 from the counter  
- `reset()` - Sets the counter back to 0

## Technologies Used

- **React 18** - UI library
- **CSS3** - Styling with modern features like gradients and animations
- **Create React App** - Build tooling

## Available Scripts

- `npm start` - Run the development server
- `npm build` - Build the app for production
- `npm test` - Run tests
- `npm eject` - Eject from Create React App (not recommended)

## Customization

You can easily customize the counter by:

- Changing colors in `App.css`
- Modifying increment/decrement values in `App.js`
- Adding new features like step size controls
- Implementing min/max limits

## License

This project is open source and available under the MIT License.