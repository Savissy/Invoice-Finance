

```markdown
# 📄 Invoice Finance — Cardano dApp

A **full-stack tokenized invoice financing platform on Cardano**, enabling verified businesses to tokenize invoices, receive funding from investors, and enforce repayment on-chain — with real-world accountability via **email authentication, KYC, wallet binding, admin review, and transaction history**.

---

## 📚 Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [System Architecture](#system-architecture)
4. [Tech Stack](#tech-stack)
5. [Smart Contract Design](#smart-contract-design)
6. [Frontend & Off-Chain Logic](#frontend--off-chain-logic)
7. [Backend & Authentication](#backend--authentication)
8. [KYC & Compliance Flow](#kyc--compliance-flow)
9. [Wallet Binding & Access Control](#wallet-binding--access-control)
10. [Transaction History](#transaction-history)
11. [Admin Portal](#admin-portal)
12. [Security Considerations](#security-considerations)
13. [Local Development Setup](#local-development-setup)
14. [Environment Configuration](#environment-configuration)
15. [Database Schema (High Level)](#database-schema-high-level)
16. [User & Platform Flow](#user--platform-flow)
17. [Limitations & Future Improvements](#limitations--future-improvements)
18. [License](#license)

---

## Overview

**Invoice Finance** is a decentralized application (dApp) built on **Cardano** that allows businesses to raise liquidity by tokenizing invoices while providing investors transparent, enforceable repayment guarantees.

The platform combines:
- **On-chain enforcement (Plutus)**
- **Off-chain coordination (Lucid)**
- **Real-world identity verification (KYC + email)**
- **Admin oversight**
- **Transaction auditability**

This is not a demo — it is a production-style architecture.

---

## Key Features

### 🧾 Invoice Financing
- Invoice tokenization (NFT-based)
- Pool deposits & investor funding
- Principal + profit repayment
- Claim creation, voting, and execution

### 🔐 Identity & Access
- Email/password authentication
- Email verification
- KYC document submission
- Admin-approved access only

### 👛 Wallet Binding
- First wallet auto-binds to user account
- Subsequent wallets blocked
- UI warnings for incorrect wallets
- Prevents identity hopping

### 📊 Transparency
- dApp-level transaction logging
- Wallet-based transaction history lookup
- Admin wallet → user → contact tracing

### 🛠 Admin Tools
- Admin login & session isolation
- KYC review & approval
- Secure document viewer
- Wallet lookup for defaulter tracing

---

## System Architecture

```

Frontend (HTML/CSS/JS)
│
▼
Lucid Off-Chain Logic
│
▼
Plutus Smart Contracts (Cardano)
│
▼
PHP Backend ─── MySQL Database
(Auth, KYC, Admin, Logs)

```

---

## Tech Stack

### On-Chain
- Haskell
- Plutus V2

### Off-Chain
- Lucid
- Blockfrost API

### Frontend
- HTML
- CSS
- Vanilla JavaScript

### Backend
- PHP
- MySQL
- PHPMailer (SMTP)

---

## Smart Contract Design

- Invoices represented as NFTs
- Datum tracks:
  - issuer
  - investors
  - repayment amount
  - repayment status
- Validators enforce:
  - funding rules
  - repayment logic
  - investor payouts
- No trust assumptions — all enforcement is on-chain

---

## Frontend & Off-Chain Logic

- Wallet connection via Lucid
- Invoice creation & funding UI
- Claim voting & execution
- Modal-based notifications
- Wallet access validation before any action

---

## Backend & Authentication

- Session-based authentication
- Secure password hashing
- CSRF protection
- Email verification via SMTP
- User ↔ wallet mapping

---

## KYC & Compliance Flow

1. User registers (email + password)
2. Email verification required
3. User submits:
   - full name
   - phone number
   - country
   - business name (optional)
   - ID document
4. Admin reviews KYC
5. Status set to `approved`, `rejected`, or `pending`

Only **approved users** can access the dApp.

---

## Wallet Binding & Access Control

- First connected wallet is permanently bound
- Wallet hash stored in backend
- Any other wallet is blocked
- Prevents evasion of repayment obligations

---

## Transaction History

- All dApp-initiated transactions are logged:
  - funding
  - repayment
  - claims
  - execution
- Searchable by wallet address
- Admin can correlate wallet to verified user

This enables **responsible defaulter tracking** within the platform.

---

## Admin Portal

Accessible at:

```

/admin/login.php

```

### Admin Capabilities
- Review KYC submissions
- View uploaded documents securely
- Approve or reject users
- Wallet lookup → user & contact details
- Oversight of platform compliance

---

## Security Considerations

- No KYC → no access
- No verified wallet → no access
- Wallet binding enforced
- Prepared SQL statements
- Secure document serving
- No private keys ever stored

---

## Local Development Setup

1. Clone the repository
2. Start MySQL and Apache (XAMPP or PHP built-in server)
3. Import database schema
4. Configure `config.php`
5. Open in browser:

```

[http://localhost/Invoice-Finance](http://localhost/Invoice-Finance)

````

---

## Environment Configuration

Key values in `config.php`:

```php
DB_HOST
DB_NAME
DB_USER
DB_PASS

APP_URL

MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_FROM_EMAIL
````

⚠️ Do not commit real credentials.

---

## Database Schema (High Level)

* `users`
* `admins`
* `kyc_submissions`
* `user_wallets`
* `invoice_transactions`
* `email_verifications`

---

## User & Platform Flow

1. Register
2. Verify email
3. Submit KYC
4. Admin approval
5. Connect wallet
6. Wallet binding
7. Invoice financing actions
8. Transaction logging
9. Admin oversight

---

## Limitations & Future Improvements

* Chain-wide transaction indexing
* Automated repayment reminders
* Risk scoring & default flags
* Multi-wallet support (optional)
* Analytics dashboard
* Custom domain email sender

---

## License

MIT License
Free to use, modify, and extend.

```

---

If you want next:
- **Grant-style README**
- **Whitepaper**
- **Architecture diagrams**
- **Demo walkthrough section**

Just tell me what you want to add.
```
