# AI-Assisted Input Validation in PHP

This project demonstrates a simple AI-assisted input validation system in PHP that uses pattern recognition and machine learning-like approaches to validate user inputs intelligently.

## Features

- **Smart Email Validation**: Beyond basic regex, checks for common typos and suspicious patterns
- **Intelligent Name Validation**: Detects potentially fake names and unusual patterns
- **Phone Number Intelligence**: Validates phone numbers and suggests corrections
- **Profanity Detection**: Basic AI-like content filtering
- **Learning System**: Adapts validation rules based on previous inputs

## Files

- `AIValidator.php` - Main validation class with AI-like logic
- `index.php` - Demo web interface to test the validation
- `README.md` - This documentation

## Usage

1. Start a PHP development server:
```bash
php -S localhost:8000
```

2. Open your browser and go to `http://localhost:8000`

3. Test the validation system with various inputs:
   - Try common email typos (gmail.co, yahoo.co)
   - Enter suspicious names (like "asdfgh" or "John123")
   - Test phone numbers with different formats
   - Try inputs with potential profanity

## How It Works

The AI validation system uses:
- Pattern recognition for common input errors
- Heuristic scoring for input quality
- Statistical analysis of character patterns
- Rule-based learning from validation history
- Contextual validation based on input type

## Example

```php
require_once 'AIValidator.php';

$validator = new AIValidator();

// Validate an email with AI assistance
$result = $validator->validateEmail("john@gmail.co"); // Common typo
if (!$result['isValid']) {
    echo $result['suggestion']; // "Did you mean john@gmail.com?"
}
```