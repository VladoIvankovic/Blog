<?php

require_once 'AIValidator.php';

// Create validator instance
$validator = new AIValidator();

echo "=== AI-Assisted Input Validation Demo ===\n\n";

// Test data
$testData = [
    'email' => 'user@example.com',
    'phone' => '555-123-4567',
    'message' => 'Hello! This is a great product. I love it!',
    'name' => 'John Doe'
];

$suspiciousData = [
    'email' => 'spam123456789@fake.co',
    'phone' => '1111111111',
    'message' => 'URGENT!!! Click here to win FREE CASH NOW!!! Limited time offer!!!',
    'name' => 'Spammer'
];

$negativeData = [
    'email' => 'angry@user.com',
    'phone' => '555-987-6543',
    'message' => 'This is stupid and awful. I hate this terrible product.',
    'name' => 'Angry User'
];

// Function to display results
function displayResults($title, $results) {
    echo "--- $title ---\n";
    foreach ($results as $field => $result) {
        echo "Field: $field\n";
        echo "Value: {$result['value']}\n";
        echo "Valid: " . ($result['valid'] ? 'Yes' : 'No') . "\n";
        echo "Confidence: {$result['confidence']}%\n";
        
        if (isset($result['reasons'])) {
            echo "Reasons: " . implode(', ', $result['reasons']) . "\n";
        }
        
        if (isset($result['formatted'])) {
            echo "Formatted: {$result['formatted']}\n";
        }
        
        if (isset($result['spam_detection'])) {
            $spam = $result['spam_detection'];
            echo "Spam Check: " . ($spam['is_spam'] ? 'SPAM' : 'Clean') . 
                 " (Score: {$spam['spam_score']})\n";
        }
        
        if (isset($result['sentiment_analysis'])) {
            $sentiment = $result['sentiment_analysis'];
            echo "Sentiment: {$sentiment['sentiment']} " .
                 "(Appropriate: " . ($sentiment['appropriate'] ? 'Yes' : 'No') . ")\n";
        }
        
        echo "\n";
    }
    echo "====================\n\n";
}

// Test 1: Valid data
$results1 = $validator->validateInput($testData);
displayResults('Valid User Data', $results1);

// Test 2: Suspicious/spam data
$results2 = $validator->validateInput($suspiciousData);
displayResults('Suspicious Data', $results2);

// Test 3: Negative sentiment data
$results3 = $validator->validateInput($negativeData);
displayResults('Negative Sentiment Data', $results3);

// Individual tests
echo "--- Individual AI Feature Tests ---\n";

// Email validation test
echo "Email Validation:\n";
$emails = ['valid@gmail.com', 'suspicious12345@fake.co', 'test@yahoo.com'];
foreach ($emails as $email) {
    $result = $validator->validateEmail($email);
    echo "$email: " . ($result['valid'] ? 'Valid' : 'Invalid') . 
         " (Confidence: {$result['confidence']}%)\n";
}

echo "\n";

// Spam detection test
echo "Spam Detection:\n";
$messages = [
    "Hello, how are you today?",
    "FREE MONEY!!! Click here NOW to win CASH!!!",
    "Thanks for the great service."
];

foreach ($messages as $message) {
    $result = $validator->detectSpam($message);
    echo "\"$message\"\n";
    echo "Spam: " . ($result['is_spam'] ? 'YES' : 'No') . 
         " (Score: {$result['spam_score']})\n\n";
}

// Sentiment analysis test
echo "Sentiment Analysis:\n";
$texts = [
    "I love this amazing product!",
    "This is terrible and stupid.",
    "It's okay, nothing special."
];

foreach ($texts as $text) {
    $result = $validator->analyzeSentiment($text);
    echo "\"$text\"\n";
    echo "Sentiment: {$result['sentiment']} " .
         "(Confidence: {$result['confidence']}%, " .
         "Appropriate: " . ($result['appropriate'] ? 'Yes' : 'No') . ")\n\n";
}

echo "Demo completed!\n";