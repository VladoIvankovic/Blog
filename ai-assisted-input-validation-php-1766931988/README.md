# AI-Assisted Input Validation in PHP

This project demonstrates AI-powered input validation using machine learning techniques to enhance traditional validation methods.

## Features

- Traditional validation (email, phone, required fields)
- AI-powered content analysis for spam detection
- Sentiment analysis for inappropriate content detection
- Profanity filtering using pattern matching
- Easy-to-use validation class

## Files

- `AIValidator.php` - Main validation class with AI-assisted features
- `example.php` - Demo showing how to use the validator
- `README.md` - This documentation

## Usage

1. Include the AIValidator class
2. Create an instance of the validator
3. Use various validation methods on your input data

```php
$validator = new AIValidator();
$result = $validator->validateEmail($email);
```

## AI Features

- **Spam Detection**: Analyzes text patterns commonly found in spam
- **Sentiment Analysis**: Detects negative sentiment and inappropriate content
- **Pattern Recognition**: Uses regex patterns enhanced with ML-like scoring

## Requirements

- PHP 7.0+
- No external dependencies required for basic functionality

## Example

Run `php example.php` to see the validator in action with sample data.