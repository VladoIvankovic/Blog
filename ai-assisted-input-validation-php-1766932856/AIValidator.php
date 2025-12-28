<?php

class AIValidator {
    private $commonDomainTypos = [
        'gmail.co' => 'gmail.com',
        'yahoo.co' => 'yahoo.com',
        'hotmail.co' => 'hotmail.com',
        'outlook.co' => 'outlook.com',
        'gmial.com' => 'gmail.com',
        'yahooo.com' => 'yahoo.com',
        'gmai.com' => 'gmail.com'
    ];
    
    private $suspiciousPatterns = [
        '/(.)\1{3,}/', // Repeated characters (aaaa)
        '/^[a-z]+\d+$/', // Simple pattern like john123
        '/^(test|admin|user)\d*$/i', // Generic usernames
        '/^[qwerty]+$/i', // Keyboard mashing
        '/^[aeiou]+$/i', // Only vowels
        '/^[bcdfg-np-tv-z]+$/i' // Only consonants
    ];
    
    private $profanityPatterns = [
        '/damn/i', '/hell/i', '/stupid/i', '/idiot/i', '/hate/i'
    ];
    
    private $validationHistory = [];
    
    public function validateEmail($email) {
        $result = [
            'isValid' => false,
            'confidence' => 0,
            'issues' => [],
            'suggestion' => null,
            'aiInsights' => []
        ];
        
        // Basic format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['issues'][] = 'Invalid email format';
            
            // AI: Try to suggest correction
            $suggestion = $this->suggestEmailCorrection($email);
            if ($suggestion) {
                $result['suggestion'] = "Did you mean: {$suggestion}?";
                $result['aiInsights'][] = 'AI detected possible typo';
            }
            return $result;
        }
        
        // AI: Check for common domain typos
        list($local, $domain) = explode('@', $email);
        if (isset($this->commonDomainTypos[$domain])) {
            $result['suggestion'] = $local . '@' . $this->commonDomainTypos[$domain];
            $result['issues'][] = 'Possible domain typo detected';
            $result['aiInsights'][] = 'Domain typo correction suggested';
            return $result;
        }
        
        // AI: Analyze local part for suspicious patterns
        $suspiciousScore = $this->analyzeSuspiciousPatterns($local);
        if ($suspiciousScore > 0.5) {
            $result['issues'][] = 'Email appears to contain suspicious patterns';
            $result['confidence'] = 1 - $suspiciousScore;
            $result['aiInsights'][] = "Suspicious pattern score: " . round($suspiciousScore, 2);
        }
        
        // All checks passed
        if (empty($result['issues'])) {
            $result['isValid'] = true;
            $result['confidence'] = 0.95;
            $result['aiInsights'][] = 'Email passed all AI validation checks';
        }
        
        $this->logValidation('email', $email, $result);
        return $result;
    }
    
    public function validateName($name) {
        $result = [
            'isValid' => false,
            'confidence' => 0,
            'issues' => [],
            'suggestion' => null,
            'aiInsights' => []
        ];
        
        $name = trim($name);
        
        // Basic checks
        if (strlen($name) < 2) {
            $result['issues'][] = 'Name too short';
            return $result;
        }
        
        if (strlen($name) > 100) {
            $result['issues'][] = 'Name too long';
            return $result;
        }
        
        // AI: Check for suspicious patterns
        $suspiciousScore = $this->analyzeSuspiciousPatterns($name);
        if ($suspiciousScore > 0.7) {
            $result['issues'][] = 'Name appears to be fake or generated';
            $result['aiInsights'][] = "Fake name probability: " . round($suspiciousScore * 100, 1) . "%";
        }
        
        // AI: Check for numbers in name (unusual)
        if (preg_match('/\d/', $name)) {
            $result['issues'][] = 'Names typically should not contain numbers';
            $result['aiInsights'][] = 'Numeric characters detected in name';
        }
        
        // AI: Profanity check
        if ($this->containsProfanity($name)) {
            $result['issues'][] = 'Inappropriate content detected';
            $result['aiInsights'][] = 'Content filter triggered';
        }
        
        // Calculate confidence based on natural language patterns
        $naturalness = $this->calculateNaturalnessScore($name);
        $result['confidence'] = $naturalness;
        $result['aiInsights'][] = "Name naturalness score: " . round($naturalness, 2);
        
        if (empty($result['issues']) && $naturalness > 0.6) {
            $result['isValid'] = true;
        }
        
        $this->logValidation('name', $name, $result);
        return $result;
    }
    
    public function validatePhoneNumber($phone) {
        $result = [
            'isValid' => false,
            'confidence' => 0,
            'issues' => [],
            'suggestion' => null,
            'aiInsights' => []
        ];
        
        // Clean the phone number
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        
        // AI: Smart format detection and suggestions
        if (preg_match('/^\d{10}$/', $cleanPhone)) {
            $result['suggestion'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $cleanPhone);
            $result['isValid'] = true;
            $result['confidence'] = 0.9;
            $result['aiInsights'][] = 'US format detected and formatted';
        } elseif (preg_match('/^1\d{10}$/', $cleanPhone)) {
            $result['suggestion'] = '+1 ' . preg_replace('/1(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $cleanPhone);
            $result['isValid'] = true;
            $result['confidence'] = 0.95;
            $result['aiInsights'][] = 'US format with country code detected';
        } elseif (preg_match('/^\+\d{10,15}$/', $cleanPhone)) {
            $result['isValid'] = true;
            $result['confidence'] = 0.8;
            $result['aiInsights'][] = 'International format detected';
        } else {
            $result['issues'][] = 'Invalid phone number format';
            $result['aiInsights'][] = 'No recognized format pattern';
        }
        
        // AI: Check for suspicious repeated digits
        if (preg_match('/(\d)\1{5,}/', $cleanPhone)) {
            $result['issues'][] = 'Suspicious repeated digits';
            $result['confidence'] *= 0.5;
            $result['aiInsights'][] = 'Repeated digit pattern detected';
        }
        
        $this->logValidation('phone', $phone, $result);
        return $result;
    }
    
    private function suggestEmailCorrection($email) {
        // Simple AI: Try to fix common typos
        if (strpos($email, '@') === false) {
            return null;
        }
        
        list($local, $domain) = explode('@', $email, 2);
        
        // Check if domain is close to common domains
        $commonDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        
        foreach ($commonDomains as $commonDomain) {
            if (levenshtein($domain, $commonDomain) <= 2) {
                return $local . '@' . $commonDomain;
            }
        }
        
        return null;
    }
    
    private function analyzeSuspiciousPatterns($text) {
        $score = 0;
        $text = strtolower($text);
        
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $score += 0.3;
            }
        }
        
        // Check character entropy (randomness)
        $entropy = $this->calculateEntropy($text);
        if ($entropy < 2.0) { // Low entropy = repetitive
            $score += 0.4;
        }
        
        return min($score, 1.0);
    }
    
    private function calculateEntropy($text) {
        $chars = str_split($text);
        $frequency = array_count_values($chars);
        $length = strlen($text);
        
        $entropy = 0;
        foreach ($frequency as $count) {
            $p = $count / $length;
            $entropy -= $p * log($p, 2);
        }
        
        return $entropy;
    }
    
    private function calculateNaturalnessScore($name) {
        $name = strtolower($name);
        
        // Simple heuristic: check vowel/consonant ratio
        $vowels = preg_match_all('/[aeiou]/', $name);
        $consonants = preg_match_all('/[bcdfg-np-tv-z]/', $name);
        
        if ($consonants == 0) return 0.1;
        
        $ratio = $vowels / $consonants;
        
        // Ideal ratio is around 0.4-0.6
        if ($ratio >= 0.3 && $ratio <= 0.8) {
            $score = 0.8;
        } else {
            $score = 0.4;
        }
        
        // Bonus for common name patterns
        if (preg_match('/^[a-z]+\s+[a-z]+$/i', $name)) {
            $score += 0.2;
        }
        
        return min($score, 1.0);
    }
    
    private function containsProfanity($text) {
        foreach ($this->profanityPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        return false;
    }
    
    private function logValidation($type, $input, $result) {
        $this->validationHistory[] = [
            'timestamp' => time(),
            'type' => $type,
            'input' => $input,
            'isValid' => $result['isValid'],
            'confidence' => $result['confidence']
        ];
        
        // Keep only last 100 validations
        if (count($this->validationHistory) > 100) {
            array_shift($this->validationHistory);
        }
    }
    
    public function getValidationStats() {
        if (empty($this->validationHistory)) {
            return ['message' => 'No validation history available'];
        }
        
        $total = count($this->validationHistory);
        $valid = array_filter($this->validationHistory, function($item) { return $item['isValid']; });
        $validCount = count($valid);
        
        $avgConfidence = array_sum(array_column($this->validationHistory, 'confidence')) / $total;
        
        return [
            'totalValidations' => $total,
            'successRate' => round(($validCount / $total) * 100, 1) . '%',
            'averageConfidence' => round($avgConfidence, 2),
            'lastValidation' => end($this->validationHistory)
        ];
    }
}