# Security & Encryption Setup Guide

This document outlines all encryption, hashing, and obfuscation libraries installed for the UA-FMS application.

## PHP/Laravel Security Libraries (Composer)

### 1. **defuse/php-encryption** v2.4
- **Purpose**: Symmetric encryption for sensitive data
- **Use**: File encryption, database field encryption
- **Docs**: https://github.com/defuse/php-encryption

### 2. **web-token/jwt-core** v3.4
- **Purpose**: JWT (JSON Web Token) implementation
- **Use**: API authentication, token generation/validation
- **Docs**: https://web-token.readthedocs.io/

### 3. **phpseclib/phpseclib** v3.0
- **Purpose**: Pure-PHP implementations of cryptographic algorithms (RSA, DSA, etc.)
- **Use**: Asymmetric encryption, digital signatures
- **Docs**: https://phpseclib.com/

### 4. **hashids/hashids** v5.1
- **Purpose**: Generate short, unique, and obfuscated IDs
- **Use**: URL-safe ID generation, resource obfuscation
- **Docs**: https://hashids.org/

### 5. **symfony/crypto** v7.0
- **Purpose**: Encryption utilities and secure random number generation
- **Use**: HMAC generation, secure hashing
- **Docs**: https://symfony.com/doc/current/components/crypto.html

### 6. **symfony/security-core** v7.0 (Dev)
- **Purpose**: Security utilities for development and testing
- **Use**: Password encoding, security utilities during development

## JavaScript/Node.js Security Libraries (NPM)

### 1. **crypto-js** v4.2.0
- **Purpose**: JavaScript cryptographic library
- **Use**: Client-side encryption, hashing, HMAC
- **Usage**: `import CryptoJS from 'crypto-js'; const encrypted = CryptoJS.AES.encrypt(data, key);`

### 2. **bcryptjs** v2.4.3
- **Purpose**: Password hashing algorithm
- **Use**: Secure password generation, validation
- **Usage**: `const hash = bcrypt.hashSync(password, 10);`

### 3. **jsonwebtoken** v9.1.2
- **Purpose**: JWT signing and verification
- **Use**: API token generation, client authentication
- **Usage**: `const token = jwt.sign(payload, secret, { expiresIn: '24h' });`

### 4. **javascript-obfuscator** v4.1.1 (Dev)
- **Purpose**: JavaScript code obfuscation
- **Use**: Frontend code protection in production builds
- **CLI**: `javascript-obfuscator input.js --output output.js`

### 5. **terser** v5.31.1 (Dev)
- **Purpose**: JavaScript minifier and mangler
- **Use**: Code minification and obfuscation during build
- **CLI**: `terser input.js -o output.js`

## Python Security Libraries (Launchers)

### 1. **cryptography** v42.0.0
- **Purpose**: Cryptographic recipes and primitives
- **Use**: Encryption/decryption, hashing in Python launchers
- **Docs**: https://cryptography.io/

### 2. **PyJWT** v2.8.1
- **Purpose**: JWT implementation for Python
- **Use**: Token generation and validation
- **Usage**: `token = jwt.encode(payload, secret, algorithm='HS256')`

### 3. **bcrypt** v4.1.2
- **Purpose**: Password hashing for Python
- **Use**: Secure password handling in launchers
- **Usage**: `hashed = bcrypt.hashpw(password, bcrypt.gensalt())`

### 4. **pyarmor** v8.4.5
- **Purpose**: Python code obfuscation and protection
- **Use**: Protect launcher source code from reverse engineering
- **CLI**: `pyarmor obfuscate launcher.py`

### 5. **PyInstaller** v6.5.0
- **Purpose**: Convert Python scripts to executables
- **Use**: Create standalone launcher executables with code protection
- **CLI**: `pyinstaller --onefile launcher.py`

## Android Security Configuration

### R8/ProGuard Obfuscation (build.gradle.kts)
- **Enabled** for release builds
- **Configuration**: `/android/ua-fms-org/app/proguard-rules.pro`
- **Features**:
  - Aggressive code obfuscation (5 optimization passes)
  - Log statement removal in release builds
  - API key and security string protection
  - Reflection attack prevention

## Installation Instructions

### Install PHP Security Libraries
```bash
cd /path/to/project
composer require defuse/php-encryption web-token/jwt-core phpseclib/phpseclib hashids/hashids symfony/crypto
```

### Install JavaScript Security Libraries
```bash
npm install
```

### Install Python Security Libraries (for launchers)
```bash
cd launchers
pip install -r requirements.txt
```

### Build Android with Obfuscation
```bash
cd android/ua-fms-org
./gradlew build --release  # Builds with R8 obfuscation enabled
```

## Security Best Practices

### 1. Data Encryption
- Use `defuse/php-encryption` for database fields containing PII
- Encrypt sensitive API responses before transmission

### 2. API Security
- Implement JWT tokens using `web-token/jwt-core`
- Use short expiration times (15-30 minutes)
- Refresh tokens should be longer-lived (7 days)

### 3. Password Storage
- Never store passwords in plain text
- Use Laravel's `Hash::make()` which uses bcrypt
- For custom implementations, use `bcryptjs` or `phpseclib`

### 4. ID Obfuscation
- Use `hashids/hashids` for API resource IDs
- Never expose sequential database IDs in URLs or APIs

### 5. Frontend Security
- Enable JavaScript obfuscation in production builds
- Use `terser` in build pipeline for minification + obfuscation
- Implement CSP (Content Security Policy) headers

### 6. Launcher Protection
- Use `pyarmor` to obfuscate Python launcher source code
- Build executables with PyInstaller for additional protection
- Sign executables with code signing certificates

### 7. Android App Security
- Release builds automatically use R8 obfuscation
- Strip debug symbols from production APKs
- Use certificate pinning for API communication

## Usage Examples

### PHP Encryption
```php
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$key = Key::createNewRandomKey();
$encrypted = Crypto::encrypt($sensitiveData, $key);
$decrypted = Crypto::decrypt($encrypted, $key);
```

### JavaScript Hashing
```javascript
import CryptoJS from 'crypto-js';
const hash = CryptoJS.SHA256("message").toString();
const encrypted = CryptoJS.AES.encrypt("message", "secret").toString();
```

### Python Encryption
```python
from cryptography.fernet import Fernet
key = Fernet.generate_key()
cipher = Fernet(key)
encrypted = cipher.encrypt(b"sensitive data")
```

## Configuration Checklist

- [ ] PHP: `composer install` to install all security libraries
- [ ] JavaScript: `npm install` to install crypto libraries
- [ ] Python: `pip install -r launchers/requirements.txt`
- [ ] Android: Update `proguard-rules.pro` with app-specific packages
- [ ] Environment: Set secure API keys and secrets in `.env`
- [ ] Testing: Verify encryption/decryption functions work correctly
- [ ] Build: Enable obfuscation in production builds
- [ ] Documentation: Document custom encryption implementations

## Security Audit Notes

- All libraries are regularly maintained and security-patched
- Consider implementing rate limiting on authentication endpoints
- Use HTTPS everywhere (especially for token transmission)
- Implement CORS properly to prevent unauthorized API access
- Regularly update dependencies: `composer update`, `npm audit fix`
- Monitor Laravel logs for security-related events
