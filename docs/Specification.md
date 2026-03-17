# Product Specification: Invoice Finance dApp

## Problem Statement

SMEs face delayed payments from buyers, leading to cash flow constraints. Traditional invoice financing systems are:

* Centralized
* Opaque
* Slow to process

This solution introduces a **decentralized, transparent, and programmable invoice financing system**.

---

## Scope

The system supports:

* Invoice creation and tokenization
* Listing invoices for funding
* Investor participation
* Repayment and settlement
* Claim dispute handling

---

## Actors

| Role              | Description                     |
| ----------------- | ------------------------------- |
| Supplier (SME)    | Creates and tokenizes invoices  |
| Buyer (Debtor)    | Owes payment (off-chain entity) |
| Investor (Factor) | Funds invoices                  |
| Auditor           | Verifies state and history      |

---

## User Journeys

### 1. Invoice Tokenization

* Supplier creates invoice
* NFT minted representing invoice
* Stored in script address

### 2. Factoring (Funding)

* Investor selects invoice
* Funds invoice
* Receives claim to repayment

### 3. Repayment

* Supplier repays invoice
* Smart contract distributes funds

### 4. Claims & Disputes (Optional)

* Users submit claims
* Voting determines execution

---

## Constraints

* eUTxO concurrency limitations
* Minimum ADA per output
* Wallet-based identity (CIP-30)
* Limited on-chain storage

---

## Acceptance Criteria

* Invoice NFT correctly represents invoice
* Funding updates on-chain state
* Repayment distributes funds accurately
* UI reflects on-chain state
* Wallet identity enforced

---
