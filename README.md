# CyberLab 🔒

Plateforme d'apprentissage en cybersécurité avec des labs de tests d'intrusion (pentesting).

![CyberLab](https://img.shields.io/badge/PHP-8%2B-blue) ![MySQL](https://img.shields.io/badge/MySQL-8-orange) ![OWASP](https://img.shields.io/badge/OWASP-Top%2010-red)

## ⚡ Labs Disponibles

| Lab | Vulnérabilité | OWASP | Difficulté |
|-----|--------------|-------|------------|
| 🗄️ SQL Injection | Union-Based, Blind, Auth Bypass | A03 | Intermédiaire |
| 💉 XSS | Reflected, Stored, DOM-Based | A03 | Débutant |
| 🔄 CSRF | GET, POST, Token Bypass | A01 | Intermédiaire |
| 📁 File Upload | Extension, MIME, WebShell | A04 | Avancé |
| 💻 Command Injection | RCE, Blind, OS Command | A03 | Avancé |
| 🔑 IDOR | URL, API, Hidden Fields | A01 | Débutant |
| 📂 LFI/Path Traversal | ../,  PHP Wrappers, Bypass | A05 | Avancé |

## 🚀 Installation

### Prérequis
- [Laragon](https://laragon.org/) (Apache + PHP + MySQL)

### Étapes

1. **Cloner le repo dans le dossier www de Laragon**
   ```bash
   cd C:\laragon\www
   git clone https://github.com/senproDiouf/Kalitest.git KaliTest
   ```

2. **Démarrer Laragon** (Apache + MySQL)

3. **Installer la base de données** via le navigateur :
   ```
   http://localhost/KaliTest/install.php
   ```
   Cliquer sur **"LANCER L'INSTALLATION"**

4. **Accéder au site** :
   ```
   http://localhost/KaliTest/
   ```

## 🔐 Comptes de Test

| Utilisateur | Mot de passe | Rôle  |
|-------------|-------------|-------|
| admin       | admin123    | Admin |
| alice       | password    | User  |
| bob         | bob123      | User  |
| hacker      | letmein     | User  |
| guest       | guest       | Guest |

## 🛠️ Outils Intégrés

- **Encodeur/Décodeur** : Base64, URL, HTML, Hex, MD5, SHA1, SHA256
- **Hash Cracker** : Attaque dictionnaire
- **Payload Generator** : XSS & SQLi prêts à l'emploi
- **HTTP Headers Analyzer**
- **Reverse Shell Generator**

## ⚠️ Avertissement Legal

> Cette plateforme est **volontairement vulnérable** et conçue uniquement pour l'apprentissage éthique de la cybersécurité.
> N'exposez jamais ce site sur Internet public. Utilisez-le uniquement dans un environnement isolé (réseau local / machine personnelle).

## 📚 Ressources

- [OWASP Top 10](https://owasp.org/Top10/)
- [PortSwigger Web Academy](https://portswigger.net/web-security)
- [HackTheBox](https://hackthebox.com)
- [TryHackMe](https://tryhackme.com)
- [PayloadsAllTheThings](https://github.com/swisskyrepo/PayloadsAllTheThings)
