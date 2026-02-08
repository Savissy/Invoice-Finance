
---

# 📄 Invoice Finance — Cardano dApp

A **full-stack, production-ready tokenized invoice financing platform** built on **Cardano**, enabling businesses to tokenize invoices, raise liquidity from investors, and enforce repayments transparently — with **on-chain logic, verified users, KYC, wallet binding, admin review, and transaction history**.

---

## 📚 Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [System Architecture](#system-architecture)
4. [Tech Stack](#tech-stack)
5. [Smart Contract Design](#smart-contract-design)
6. [Off-Chain & Frontend](#off-chain--frontend)
7. [Backend & Authentication](#backend--authentication)
8. [KYC & Compliance Flow](#kyc--compliance-flow)
9. [Wallet Binding & Access Control](#wallet-binding--access-control)
10. [Transaction History](#transaction-history)
11. [Admin Portal](#admin-portal)
12. [Security Considerations](#security-considerations)
13. [Local Development Setup](#local-development-setup)
14. [Environment Configuration](#environment-configuration)
15. [Database Schema (High Level)](#database-schema-high-level)
16. [Usage Flow](#usage-flow)
17. [Limitations & Future Improvements](#limitations--future-improvements)
18. [License](#license)

---

## Overview

**Invoice Finance** allows verified businesses to:

* tokenize invoices on Cardano,
* receive funding from investors,
* repay principal + profit on-chain,
* and maintain **real-world accountability** via KYC and wallet binding.

Unlike simple DeFi demos, this platform integrates:

* **real user identity verification**
* **admin oversight**
* **transaction auditability**
* **wallet-to-account enforcement**

---

## Key Features

### 🧾 Invoice Financing

* Invoice tokenization (NFT-based)
* Pool funding by investors
* Repayment with profit
* Claim creation, voting, and execution

### 🔐 Authentication & Identity

* Email + password login
* Email verification (SMTP)
* KYC document upload
* Admin KYC approval

### 👛 Wallet Control

* Wallet binding to verified user account
* First wallet auto-bind
* Subsequent wallet access blocked
* UI warnings for incorrect wallets

### 📊 Transparency

* dApp-level transaction history
* Wallet-based transaction lookup
* Admin wallet → user → KYC mapping

### 🛠 Admin Tools

* Admin authentication
* KYC review & approval
* Secure document viewer
* Wallet lookup & defaulter tracing

---

## System Architecture

```
┌────────────┐     ┌────────────────┐
│  Frontend  │◄───►│  PHP Backend   │
│ (HTML/JS)  │     │ (Auth, KYC)    │
└─────┬──────┘     └───────┬────────┘
      │                    │
      ▼                    ▼
┌────────────┐     ┌────────────────┐
│  Lucid JS  │     │   MySQL DB     │
│ (Offchain) │     │ (Users, KYC)   │
└─────┬──────┘     └───────┬────────┘
      │                    │
      ▼                    ▼
┌──────────────────────────────────┐
│      Cardano Blockchain          │
│   (Plutus Smart Contracts)       │
└──────────────────────────────────┘
```

---

## Tech Stack

### On-Chain

* **Haskell**
* **Plutus V2**

### Off-Chain

* **Lucid**
* **Blockfrost API**

### Frontend

* **HTML**
* **CSS**
* **Vanilla JavaScript**

### Backend

* **PHP**
* **MySQL**
* **PHPMailer (SMTP)**

### Tooling

* XAMPP / PHP built-in server
* phpMyAdmin

---

## Smart Contract Design

* Invoice validator enforces:

  * funding rules
  * repayment conditions
  * investor payouts
  * invoice state transitions
* NFTs represent unique invoices
* Datum tracks:

  * issuer
  * investors
  * repayment terms
  * repayment status

All enforcement is **on-chain**, not trust-based.

---

## Off-Chain & Frontend

* Wallet connection via Lucid
* Invoice creation & funding UI
* Claim voting & execution
* Modal-based UX feedback
* Wallet access enforcement

---

## Backend & Authentication

* Secure session-based auth
* Password hashing (`password_hash`)
* CSRF protection
* Email verification via SMTP
* User → wallet mapping

---

## KYC & Compliance Flow

1. User registers (email + password)
2. Email verification required
3. User submits:

   * full name
   * phone number
   * country
   * business name (optional)
   * ID document
4. Admin reviews submission
5. Status set to:

   * `approved`
   * `rejected`
   * `pending`

Only **approved users** access the dApp.

---

## Wallet Binding & Access Control

* First wallet used → automatically bound
* Wallet address hashed and stored
* Subsequent wallet connections:

  * blocked
  * user warned via modal
* Prevents:

  * identity hopping
  * wallet switching to evade obligations

---

## Transaction History

* All **dApp-initiated transactions** are logged
* Includes:

  * funding
  * repayments
  * claims
  * executions
* Searchable by wallet address
* Admin-only correlation to:

  * user account
  * KYC details

This enables **defaulter tracking** without violating blockchain privacy.

---

## Admin Portal

Accessible at:

```
/admin/login.php
```

### Admin Capabilities

* Review KYC submissions
* View uploaded documents securely
* Approve / reject users
* Lookup wallet addresses
* Trace wallet → user → contact details

Admin actions are fully separated from user accounts.

---

## Security Considerations

* No wallet = no access
* No KYC = no access
* Wallet binding enforced
* CSRF protection
* Prepared SQL statements
* Secure document serving
* No private keys ever stored

---

## Local Development Setup

1. Clone repo
2. Start **MySQL** and **Apache**
3. Configure database
4. Update `config.php`
5. Run via:

   ```
   http://localhost/Invoice-Finance
   ```

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
```

⚠️ Do **not** commit real credentials.

---

## Database Schema (High Level)

* `users`
* `admins`
* `kyc_submissions`
* `user_wallets`
* `invoice_transactions`
* `email_verifications`

---

## Usage Flow

1. User registers
2. Verifies email
3. Completes KYC
4. Admin approves
5. User connects wallet
6. Wallet bound
7. Invoice financing enabled
8. Transactions logged
9. Admin oversight active

---

## Limitations & Future Improvements

* Chain-wide transaction indexing (optional)
* Cron-based tx confirmation checks
* Risk scoring & default flags
* Multi-wallet support (optional)
* Custom domain email sender
* UI dashboard analytics

---

## License

MIT License
Free to use, modify, and extend.

---
