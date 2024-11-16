<?php
class MFAController {
    private $codeLength = 6;
    private $codeExpiry = 300; // 5 minutes in seconds
    
    // Store temporary codes in local storage (for prototype/offline demo)
    private $tempCodeFile = 'temp/mfa_codes.json';
    
    public function __construct() {
        if (!file_exists('temp')) {
            mkdir('temp', 0777, true);
        }
    }
    
    // Generate a random verification code
    public function generateCode($username) {
        $code = str_pad(rand(0, pow(10, $this->codeLength) - 1), $this->codeLength, '0', STR_PAD_LEFT);
        $expiry = time() + $this->codeExpiry;
        
        $codes = $this->loadCodes();
        $codes[$username] = [
            'code' => $code,
            'expiry' => $expiry
        ];
        
        $this->saveCodes($codes);
        return $code;
    }
    
    // Verify the code entered by user
    public function verifyCode($username, $code) {
        $codes = $this->loadCodes();
        
        if (!isset($codes[$username])) {
            return false;
        }
        
        $stored = $codes[$username];
        if (time() > $stored['expiry']) {
            $this->removeCode($username);
            return false;
        }
        
        if ($stored['code'] === $code) {
            $this->removeCode($username);
            return true;
        }
        
        return false;
    }
    
    private function loadCodes() {
        if (file_exists($this->tempCodeFile)) {
            return json_decode(file_get_contents($this->tempCodeFile), true) ?? [];
        }
        return [];
    }
    
    private function saveCodes($codes) {
        file_put_contents($this->tempCodeFile, json_encode($codes));
    }
    
    private function removeCode($username) {
        $codes = $this->loadCodes();
        unset($codes[$username]);
        $this->saveCodes($codes);
    }
}