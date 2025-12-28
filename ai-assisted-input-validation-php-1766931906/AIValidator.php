<?php

class AIValidator {
    
    private $suspiciousPatterns;
    private $profanityList;
    private $sqlKeywords;
    
    public function __construct() {
        $this->initializePatterns();
    }
    
    private function initializePatterns() {
        // SQL injection patterns
        $this->sqlKeywords = [
            'union', 'select', 'insert', 'delete', 'update', 'drop', 
            'create', 'alter', 'exec', 'execute', 'script', 'javascript',
            'onload', 'onerror', 'onclick', '<script', '</script>'
        ];
        
        // Suspicious patterns for various attacks
        $this->suspiciousPatterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(<script[^>]*>)/i',
            '/(javascript:)/i',
            '/(\bon\w+\s*=)/i',
            '/(\beval\s*\()/i',
            '/(\balert\s*\()/i'
        ];
        
        // Basic profanity list (keeping it mild for example)
        $this->profanityList = [
            'spam', 'scam', 'fake', 'stupid', 'hate', 'damn'
        ];
    }
    
    /**
     * AI-enhanced email validation
     */
    public function validateEmail($email) {
        $result = [
            'valid' => false,
            'confidence' => 0,
            'issues' => [],
            'ai_analysis' => []
        ];
        
        // Basic format check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['issues'][] = 'Invalid email format';
            return $result;
        }
        
        // AI analysis
        $confidence = 50; // Base confidence
        
        // Check for suspicious patterns
        if ($this->containsSuspiciousContent($email)) {
            $result['issues'][] = 'Contains suspicious patterns';
            $confidence -= 30;
        }
        
        // Domain analysis
        $domain = substr(strrchr($email, "@"), 1);
        if ($this->analyzeDomain($domain)) {
            $confidence += 20;
            $result['ai_analysis'][] = 'Domain appears legitimate';
        }
        
        // Length and complexity analysis
        if (strlen($email) > 5 && strlen($email) < 100) {
            $confidence += 10;
        }
        
        $result['valid'] = $confidence > 60;
        $result['confidence'] = max(0, min(100, $confidence));
        
        return $result;
    }
    
    /**
     * AI-enhanced phone validation
     */
    public function validatePhone($phone) {
        $result = [
            'valid' => false,
            'confidence' => 0,
            'issues' => [],
            'ai_analysis' => [],
            'formatted' => ''
        ];
        
        // Clean the phone number
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // AI pattern analysis
        $confidence = 40;
        
        // International format detection
        if (preg_match('/^\+\d{1,3}\d{6,14}$/', $cleaned)) {
            $confidence += 30;
            $result['ai_analysis'][] = 'International format detected';
        } elseif (preg_match('/^\d{10}$/', $cleaned)) {
            $confidence += 25;
            $result['ai_analysis'][] = 'Standard 10-digit format';
        }
        
        // Length analysis
        $length = strlen($cleaned);
        if ($length >= 10 && $length <= 15) {
            $confidence += 20;
        } else {
            $result['issues'][] = 'Invalid phone number length';
            $confidence -= 20;
        }
        
        // Pattern recognition for common formats
        if (preg_match('/^(\+1)?[2-9]\d{2}[2-9]\d{2}\d{4}$/', $cleaned)) {
            $confidence += 15;
            $result['ai_analysis'][] = 'Valid North American format';
        }
        
        $result['valid'] = $confidence > 70;
        $result['confidence'] = max(0, min(100, $confidence));
        $result['formatted'] = $this->formatPhone($cleaned);
        
        return $result;
    }
    
    /**
     * AI-powered content moderation
     */
    public function moderateContent($content) {
        $result = [
            'approved' => true,
            'confidence' => 90,
            'issues' => [],
            'ai_analysis' => [],
            'risk_score' => 0
        ];
        
        $riskScore = 0;
        
        // SQL injection detection
        if ($this->detectSQLInjection($content)) {
            $result['issues'][] = 'Potential SQL injection detected';
            $riskScore += 40;
            $result['ai_analysis'][] = 'High-risk SQL patterns found';
        }
        
        // XSS detection
        if ($this->detectXSS($content)) {
            $result['issues'][] = 'Potential XSS attack detected';
            $riskScore += 35;
            $result['ai_analysis'][] = 'Script injection patterns detected';
        }
        
        // Profanity check
        $profanityScore = $this->checkProfanity($content);
        if ($profanityScore > 0) {
            $result['issues'][] = 'Inappropriate language detected';
            $riskScore += $profanityScore * 10;
        }
        
        // Content analysis
        $sentiment = $this->analyzeSentiment($content);
        $result['ai_analysis'][] = "Sentiment: {$sentiment['label']} (confidence: {$sentiment['confidence']}%)";
        
        if ($sentiment['label'] === 'negative' && $sentiment['confidence'] > 80) {
            $riskScore += 15;
        }
        
        $result['risk_score'] = min(100, $riskScore);
        $result['confidence'] = max(10, 100 - $riskScore);
        $result['approved'] = $riskScore < 50;
        
        return $result;
    }
    
    private function containsSuspiciousContent($input) {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    private function analyzeDomain($domain) {
        // Simple domain legitimacy check
        $legitimateTLDs = ['com', 'org', 'net', 'edu', 'gov', 'mil'];
        $tld = substr(strrchr($domain, "."), 1);
        return in_array(strtolower($tld), $legitimateTLDs);
    }
    
    private function formatPhone($phone) {
        if (strlen($phone) === 10) {
            return sprintf("(%s) %s-%s", 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6)
            );
        }
        return $phone;
    }
    
    private function detectSQLInjection($input) {
        $input = strtolower($input);
        $suspiciousCount = 0;
        
        foreach ($this->sqlKeywords as $keyword) {
            if (strpos($input, strtolower($keyword)) !== false) {
                $suspiciousCount++;
            }
        }
        
        return $suspiciousCount >= 2;
    }
    
    private function detectXSS($input) {
        $xssPatterns = [
            '/<script[^>]*>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>/i',
            '/eval\s*\(/i',
            '/alert\s*\(/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    private function checkProfanity($content) {
        $content = strtolower($content);
        $count = 0;
        
        foreach ($this->profanityList as $word) {
            if (strpos($content, strtolower($word)) !== false) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function analyzeSentiment($text) {
        // Simple sentiment analysis
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic'];
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'disgusting', 'hate'];
        
        $text = strtolower($text);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($text, $word);
        }
        
        if ($positiveCount > $negativeCount) {
            return ['label' => 'positive', 'confidence' => min(95, 60 + ($positiveCount * 10))];
        } elseif ($negativeCount > $positiveCount) {
            return ['label' => 'negative', 'confidence' => min(95, 60 + ($negativeCount * 10))];
        } else {
            return ['label' => 'neutral', 'confidence' => 70];
        }
    }
}