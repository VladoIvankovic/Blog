<?php

class AIValidator {
    
    private $spamKeywords = [
        'free', 'win', 'cash', 'money', 'prize', 'urgent', 'limited time',
        'click here', 'buy now', 'guarantee', 'no risk', 'act now'
    ];
    
    private $profanityPatterns = [
        '/\b(spam|fake|scam|fraud)\b/i',
        '/\b(hate|stupid|idiot|moron)\b/i',
        '/\b(f[u\*]ck|sh[i\*]t|d[a\*]mn)\b/i'
    ];
    
    private $positiveWords = ['good', 'great', 'excellent', 'awesome', 'love', 'like', 'amazing'];
    private $negativeWords = ['bad', 'terrible', 'hate', 'awful', 'horrible', 'stupid', 'worst'];

    /**
     * Validate email with AI-enhanced checks
     */
    public function validateEmail($email) {
        $result = ['valid' => false, 'confidence' => 0, 'reasons' => []];
        
        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['reasons'][] = 'Invalid email format';
            return $result;
        }
        
        // AI-enhanced validation
        $confidence = 50; // Base confidence for valid format
        
        // Check for suspicious patterns
        if (preg_match('/\d{5,}/', $email)) {
            $confidence -= 20;
            $result['reasons'][] = 'Suspicious: Too many numbers';
        }
        
        // Check domain reputation (simplified)
        $domain = substr(strrchr($email, "@"), 1);
        $trustedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
        
        if (in_array($domain, $trustedDomains)) {
            $confidence += 30;
        } elseif (strlen($domain) < 4 || !strpos($domain, '.')) {
            $confidence -= 25;
            $result['reasons'][] = 'Suspicious domain';
        }
        
        $result['valid'] = $confidence >= 60;
        $result['confidence'] = min(100, max(0, $confidence));
        
        return $result;
    }
    
    /**
     * AI-powered spam detection
     */
    public function detectSpam($text) {
        $spamScore = 0;
        $text = strtolower($text);
        
        // Check for spam keywords
        foreach ($this->spamKeywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $spamScore += 15;
            }
        }
        
        // Check for excessive capitalization
        $upperCaseRatio = strlen(preg_replace('/[^A-Z]/', '', $text)) / strlen($text);
        if ($upperCaseRatio > 0.3) {
            $spamScore += 20;
        }
        
        // Check for excessive punctuation
        $punctuationCount = preg_match_all('/[!?]{2,}/', $text);
        $spamScore += $punctuationCount * 10;
        
        // Check for suspicious URLs
        if (preg_match_all('/http[s]?:\/\//', $text) > 2) {
            $spamScore += 25;
        }
        
        return [
            'is_spam' => $spamScore > 50,
            'spam_score' => min(100, $spamScore),
            'confidence' => min(100, $spamScore * 2)
        ];
    }
    
    /**
     * Sentiment analysis for content appropriateness
     */
    public function analyzeSentiment($text) {
        $text = strtolower($text);
        $words = preg_split('/\W+/', $text);
        
        $positiveScore = 0;
        $negativeScore = 0;
        
        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeScore++;
            }
        }
        
        // Check for profanity patterns
        foreach ($this->profanityPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $negativeScore += 3;
            }
        }
        
        $totalWords = count($words);
        $sentiment = 'neutral';
        $confidence = 0;
        
        if ($totalWords > 0) {
            $positiveRatio = $positiveScore / $totalWords;
            $negativeRatio = $negativeScore / $totalWords;
            
            if ($positiveRatio > $negativeRatio && $positiveRatio > 0.1) {
                $sentiment = 'positive';
                $confidence = min(100, $positiveRatio * 500);
            } elseif ($negativeRatio > $positiveRatio && $negativeRatio > 0.1) {
                $sentiment = 'negative';
                $confidence = min(100, $negativeRatio * 500);
            } else {
                $confidence = 60; // Neutral confidence
            }
        }
        
        return [
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'appropriate' => $sentiment !== 'negative' || $confidence < 70,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore
        ];
    }
    
    /**
     * Validate phone number with pattern recognition
     */
    public function validatePhone($phone) {
        // Clean the phone number
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);
        
        $result = ['valid' => false, 'confidence' => 0, 'formatted' => ''];
        
        // Check length and patterns
        if (strlen($cleanPhone) === 10) {
            $result['valid'] = true;
            $result['confidence'] = 90;
            $result['formatted'] = substr($cleanPhone, 0, 3) . '-' . 
                                 substr($cleanPhone, 3, 3) . '-' . 
                                 substr($cleanPhone, 6, 4);
        } elseif (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) === '1') {
            $result['valid'] = true;
            $result['confidence'] = 95;
            $cleanPhone = substr($cleanPhone, 1); // Remove country code
            $result['formatted'] = '+1-' . substr($cleanPhone, 0, 3) . '-' . 
                                 substr($cleanPhone, 3, 3) . '-' . 
                                 substr($cleanPhone, 6, 4);
        }
        
        // AI pattern checks
        if ($result['valid']) {
            // Check for suspicious patterns (repeated digits)
            if (preg_match('/(\d)\1{6,}/', $cleanPhone)) {
                $result['confidence'] -= 30;
                $result['valid'] = $result['confidence'] > 50;
            }
            
            // Check for sequential patterns
            if (preg_match('/0123456789|1234567890/', $cleanPhone)) {
                $result['confidence'] -= 40;
                $result['valid'] = $result['confidence'] > 50;
            }
        }
        
        return $result;
    }
    
    /**
     * Comprehensive input validation
     */
    public function validateInput($data) {
        $results = [];
        
        foreach ($data as $field => $value) {
            $fieldResult = ['field' => $field, 'value' => $value];
            
            if ($field === 'email') {
                $fieldResult = array_merge($fieldResult, $this->validateEmail($value));
            } elseif ($field === 'phone') {
                $fieldResult = array_merge($fieldResult, $this->validatePhone($value));
            } elseif (in_array($field, ['message', 'comment', 'description'])) {
                $spamCheck = $this->detectSpam($value);
                $sentimentCheck = $this->analyzeSentiment($value);
                
                $fieldResult['spam_detection'] = $spamCheck;
                $fieldResult['sentiment_analysis'] = $sentimentCheck;
                $fieldResult['valid'] = !$spamCheck['is_spam'] && $sentimentCheck['appropriate'];
                $fieldResult['confidence'] = min($spamCheck['confidence'], $sentimentCheck['confidence']);
            } else {
                // Basic validation for other fields
                $fieldResult['valid'] = !empty(trim($value));
                $fieldResult['confidence'] = $fieldResult['valid'] ? 100 : 0;
            }
            
            $results[$field] = $fieldResult;
        }
        
        return $results;
    }
}