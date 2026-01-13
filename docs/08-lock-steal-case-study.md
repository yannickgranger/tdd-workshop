# 08 - SOFTWARE Case Study: Lock Steal Authorization

> **Real-world TDD walkthrough** from a 15+ year legacy medical imaging system (enterprise software).

This document demonstrates how TDD was applied to implement a **role-based access control (RBAC)** feature for lock stealing in a production system.

---

## Table of Contents

1. [Business Context](#1-business-context)
2. [The Problem](#2-the-problem)
3. [TDD Walkthrough](#3-tdd-walkthrough)
4. [Edge Case Discovery](#4-edge-case-discovery)
5. [The Builder Pattern](#5-the-builder-pattern)
6. [Final Production Code](#6-final-production-code)
7. [Symfony Integration](#7-symfony-integration)
8. [Key Takeaways](#8-key-takeaways)

---

## 1. Business Context

### What is SOFTWARE?

SOFTWARE is a **Radiology Information System (RIS)** used in hospitals and imaging centers. It manages:
- Patient records and appointments
- Medical examinations (X-rays, MRI, CT scans)
- Doctor reports and signatures
- Billing and administrative tasks

### The Lock System

In SOFTWARE, when a doctor opens a patient's examination to write a report, the system **locks** that record:
- Prevents concurrent edits (data corruption)
- Shows other users who is working on it
- Auto-releases when the user finishes

### The Problem: Lock Conflicts

**Scenario**: Dr. Smith is writing a report. The secretary needs to update administrative information but can't - the record is locked.

**Old behavior**: Secretary has to wait or ask Dr. Smith to release the lock.

**New requirement**: Implement **lock stealing** with proper authorization.

---

## 2. The Problem

### Business Rules (from the Product Owner)

After discussion with the medical staff, these rules emerged:

```
1. Doctors can steal locks from secretaries
   → Patient care takes priority over admin tasks

2. Secretaries CANNOT steal from doctors
   → Never interrupt a doctor writing a medical report

3. Same-tier users CANNOT steal from each other
   → Doctor cannot steal from doctor (both equally important)
   → Secretary cannot steal from secretary (first-come-first-served)
```

### The Technical Challenge

How do we determine if someone is a "doctor" vs a "secretary"?

In SOFTWARE, staff members have **capability flags**:

| Flag | Meaning |
|------|---------|
| `st_sign` | Can sign medical reports (doctors, radiologists) |
| `st_ope` | Can perform operations |
| `st_valid` | Can validate examinations |
| `st_invoice` | Can handle billing |

**Decision**: `st_sign = true` → Doctor tier, `st_sign = false` → Secretary tier

---

## 3. TDD Walkthrough

### Cycle 1: The Happy Path (RED)

We start with the **simplest test that could possibly fail**.

```php
<?php
// tests/Unit/Core/Application/Lock/Authorization/LockStealAuthorizationServiceTest.php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Lock\Authorization;

use PHPUnit\Framework\TestCase;

final class LockStealAuthorizationServiceTest extends TestCase
{
    public function test_doctor_can_steal_from_secretary(): void
    {
        // Arrange
        $service = new LockStealAuthorizationService();

        $doctorPermissions = ['st_sign' => true];
        $secretaryPermissions = ['st_sign' => false];

        // Act
        $result = $service->canStealFrom($doctorPermissions, $secretaryPermissions);

        // Assert
        $this->assertTrue($result);
    }
}
```

**Run the test:**

```bash
$ make test

Error: Class "LockStealAuthorizationService" not found
```

**Status: RED** ✅ - The test fails because the class doesn't exist.

---

### Cycle 1: Make it GREEN

Write the **minimum code** to pass the test:

```php
<?php
// src/Core/Application/Lock/Authorization/LockStealAuthorizationService.php

declare(strict_types=1);

namespace App\Core\Application\Lock\Authorization;

final class LockStealAuthorizationService
{
    public function canStealFrom(array $requesterPermissions, array $holderPermissions): bool
    {
        return true; // Minimal implementation
    }
}
```

**Run the test:**

```bash
$ make test

OK (1 test, 1 assertion)
```

**Status: GREEN** ✅ - But we're not done. This implementation is too naive.

---

### Cycle 2: The Negative Case (RED)

Now we add a test that **forces us to write real logic**:

```php
public function test_secretary_cannot_steal_from_doctor(): void
{
    // Arrange
    $service = new LockStealAuthorizationService();

    $secretaryPermissions = ['st_sign' => false];
    $doctorPermissions = ['st_sign' => true];

    // Act
    $result = $service->canStealFrom($secretaryPermissions, $doctorPermissions);

    // Assert
    $this->assertFalse($result, 'Secretary should NOT be able to steal from doctor');
}
```

**Run the test:**

```bash
$ make test

FAILURES!
test_secretary_cannot_steal_from_doctor
Failed asserting that true is false.
```

**Status: RED** ✅ - Good! Our naive implementation is exposed.

---

### Cycle 2: Implement Real Logic (GREEN)

```php
final class LockStealAuthorizationService
{
    public function canStealFrom(array $requesterPermissions, array $holderPermissions): bool
    {
        $requesterIsDoctor = $this->isDoctor($requesterPermissions);
        $holderIsDoctor = $this->isDoctor($holderPermissions);

        // Doctor can steal from secretary (non-doctor)
        // But not vice versa, and not same-tier
        return $requesterIsDoctor && !$holderIsDoctor;
    }

    private function isDoctor(array $permissions): bool
    {
        return $permissions['st_sign'] === true;
    }
}
```

**Run the test:**

```bash
$ make test

OK (2 tests, 2 assertions)
```

**Status: GREEN** ✅

---

### Cycle 3: Same-Tier Cases (RED → GREEN)

Let's verify same-tier users cannot steal from each other:

```php
public function test_doctor_cannot_steal_from_doctor(): void
{
    $service = new LockStealAuthorizationService();

    $doctor1 = ['st_sign' => true];
    $doctor2 = ['st_sign' => true];

    $result = $service->canStealFrom($doctor1, $doctor2);

    $this->assertFalse($result, 'Doctor should NOT steal from another doctor');
}

public function test_secretary_cannot_steal_from_secretary(): void
{
    $service = new LockStealAuthorizationService();

    $secretary1 = ['st_sign' => false];
    $secretary2 = ['st_sign' => false];

    $result = $service->canStealFrom($secretary1, $secretary2);

    $this->assertFalse($result, 'Secretary should NOT steal from another secretary');
}
```

**Run the tests:**

```bash
$ make test

OK (4 tests, 4 assertions)
```

**Status: GREEN** ✅ - These pass immediately! Our logic already handles these cases.

> **TDD Insight**: Sometimes new tests pass immediately. That's fine - it means our implementation is more general than we thought. But we keep the tests as **documentation** and **regression protection**.

---

## 4. Edge Case Discovery

This is where TDD really shines. **Writing tests forces us to think about edge cases.**

### Discovery 1: Legacy Database Types

While writing tests, a team member asks:

> "Wait, in the legacy database, `st_sign` is stored as `1` or `0`, not `true` or `false`. Will this work?"

**RED - Write the test:**

```php
public function test_doctor_can_steal_with_numeric_st_sign(): void
{
    $service = new LockStealAuthorizationService();

    // Legacy DB returns integers, not booleans
    $doctor = ['st_sign' => 1];
    $secretary = ['st_sign' => 0];

    $result = $service->canStealFrom($doctor, $secretary);

    $this->assertTrue($result, 'Should handle integer st_sign from legacy DB');
}
```

**Run the test:**

```bash
$ make test

FAILURES!
test_doctor_can_steal_with_numeric_st_sign
Failed asserting that false is true.
```

**Status: RED** ✅ - Bug found before production!

**GREEN - Fix the implementation:**

```php
private function isDoctor(array $permissions): bool
{
    // Handle bool, int, and string types from various DB sources
    return (bool) ($permissions['st_sign'] ?? false);
}
```

**Run the test:**

```bash
$ make test

OK (5 tests, 5 assertions)
```

---

### Discovery 2: String Values

Another edge case - some legacy code passes string `"1"` or `"0"`:

```php
public function test_doctor_can_steal_with_string_st_sign(): void
{
    $service = new LockStealAuthorizationService();

    // Some legacy code passes strings
    $doctor = ['st_sign' => '1'];
    $secretary = ['st_sign' => '0'];

    $result = $service->canStealFrom($doctor, $secretary);

    $this->assertTrue($result, 'Should handle string st_sign');
}
```

This test **passes immediately** thanks to PHP's type juggling with `(bool)`.

---

### Discovery 3: Missing Permission

What if `st_sign` is not set at all?

```php
public function test_missing_st_sign_treated_as_non_doctor(): void
{
    $service = new LockStealAuthorizationService();

    $doctor = ['st_sign' => true];
    $unknownUser = []; // No st_sign key

    $result = $service->canStealFrom($doctor, $unknownUser);

    // Missing = false = non-doctor = can be stolen from
    $this->assertTrue($result, 'Missing st_sign should be treated as non-doctor');
}

public function test_requester_without_st_sign_cannot_steal(): void
{
    $service = new LockStealAuthorizationService();

    $unknownUser = []; // No st_sign
    $secretary = ['st_sign' => false];

    $result = $service->canStealFrom($unknownUser, $secretary);

    // Unknown user is not a doctor, so cannot steal
    $this->assertFalse($result);
}
```

Both tests **pass** thanks to the `?? false` null coalescing in `isDoctor()`.

---

### Discovery 4: Null Values

What if `st_sign` is explicitly `null`?

```php
public function test_null_st_sign_treated_as_non_doctor(): void
{
    $service = new LockStealAuthorizationService();

    $doctor = ['st_sign' => true];
    $nullUser = ['st_sign' => null];

    $result = $service->canStealFrom($doctor, $nullUser);

    $this->assertTrue($result, 'null st_sign should be treated as non-doctor');
}
```

This test **passes** - `(bool) null` is `false`.

---

### Test Summary After Edge Cases

```bash
$ make test

OK (9 tests, 9 assertions)

Tests:
 ✓ test_doctor_can_steal_from_secretary
 ✓ test_secretary_cannot_steal_from_doctor
 ✓ test_doctor_cannot_steal_from_doctor
 ✓ test_secretary_cannot_steal_from_secretary
 ✓ test_doctor_can_steal_with_numeric_st_sign
 ✓ test_doctor_can_steal_with_string_st_sign
 ✓ test_missing_st_sign_treated_as_non_doctor
 ✓ test_requester_without_st_sign_cannot_steal
 ✓ test_null_st_sign_treated_as_non_doctor
```

> **TDD Value**: We discovered 4 edge cases **before** the code reached production. Each edge case is now **documented as a test** and will catch regressions forever.

---

## 5. The Builder Pattern

### The Problem with Our Tests

Our tests use raw arrays, but in production, permissions come from a `CurrentUserContext` object:

```php
// Production code needs this:
$service->canStealFrom($currentUserContext, $holderPermissions);
```

### Why Not Mock?

We could use mocks:

```php
// ❌ DON'T DO THIS
$mockContext = $this->createMock(CurrentUserContext::class);
$mockContext->method('getStaffCapabilities')->willReturn(['st_sign' => true]);
```

**Problems with mocks:**
1. **Mocks lie** - They simulate behavior that may differ from reality
2. **Fragile** - Tests break when method signatures change
3. **Verbose** - Lots of setup code
4. **False confidence** - Tests pass but production fails

### The Builder Pattern

Instead, we create a **Builder** that constructs real objects:

```php
<?php
// tests/_Setup/Builder/CurrentUserContextBuilder.php

declare(strict_types=1);

namespace App\Tests\_Setup\Builder;

use App\Bridge\Application\CurrentUserContext;

final class CurrentUserContextBuilder
{
    private int $id = 1;
    private string $username = 'test_user';
    private int $currentSiteId = 1;
    private array $staffCapabilities = [];
    // ... other defaults

    public static function create(): self
    {
        return new self();
    }

    public function withUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function withStaffCapabilities(array $capabilities): self
    {
        $this->staffCapabilities = $capabilities;
        return $this;
    }

    public function build(): CurrentUserContext
    {
        return new CurrentUserContext(
            id: $this->id,
            username: $this->username,
            currentSiteId: $this->currentSiteId,
            staffCapabilities: $this->staffCapabilities,
            // ... other properties with defaults
        );
    }
}
```

### Using the Builder in Tests

```php
public function test_doctor_can_steal_from_secretary(): void
{
    // Arrange - Using Builder (readable, real objects)
    $doctor = CurrentUserContextBuilder::create()
        ->withUsername('DR_SMITH')
        ->withStaffCapabilities(['st_sign' => true])
        ->build();

    $secretaryPermissions = ['st_sign' => false];

    // Act
    $result = $this->service->canStealFrom($doctor, $secretaryPermissions);

    // Assert
    $this->assertTrue($result, 'Doctor should be able to steal from secretary');
}
```

### Builder Benefits

| Aspect | Mock | Builder |
|--------|------|---------|
| Object type | Fake | Real |
| Behavior | Simulated | Actual |
| Refactoring | Breaks easily | Adapts |
| Readability | Verbose | Fluent |
| Confidence | Low | High |

---

## 6. Final Production Code

### The Service

```php
<?php
// src/Core/Application/Lock/Authorization/LockStealAuthorizationService.php

declare(strict_types=1);

namespace App\Core\Application\Lock\Authorization;

use App\Bridge\Application\CurrentUserContext;

/**
 * RBAC authorization for lock stealing
 *
 * Business Rule:
 * - Doctors (st_sign=true) can steal locks from secretaries (st_sign=false)
 * - Secretaries cannot steal from doctors
 * - Same-tier users cannot steal from each other
 *
 * Rationale:
 * - Doctor working → secretary should wait (non-urgent administrative tasks)
 * - Secretary on vacation → doctor needs immediate access (patient care priority)
 *
 * This creates a simple two-tier priority system without complex role hierarchies.
 */
final readonly class LockStealAuthorizationService
{
    /**
     * Check if requester can steal lock from current holder
     *
     * @param CurrentUserContext $requester User attempting to steal the lock
     * @param array<string, bool> $lockHolderPermissions ST_* flags of current lock holder
     * @return bool True if steal is authorized
     */
    public function canStealFrom(
        CurrentUserContext $requester,
        array $lockHolderPermissions
    ): bool {
        $requesterIsDoctor = $this->isDoctor($requester->getStaffCapabilities());
        $holderIsDoctor = $this->isDoctor($lockHolderPermissions);

        // Doctor can steal from secretary, not vice versa
        // Same-tier cannot steal from each other
        return $requesterIsDoctor && !$holderIsDoctor;
    }

    /**
     * Determine if a user has doctor privileges based on permissions
     *
     * @param array<string, bool> $permissions User permission flags
     * @return bool True if user has signing authority (doctor/radiologist)
     */
    private function isDoctor(array $permissions): bool
    {
        return (bool) ($permissions['st_sign'] ?? false);
    }
}
```

### The Complete Test Suite

```php
<?php
// tests/Unit/Core/Application/Lock/Authorization/LockStealAuthorizationServiceTest.php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Lock\Authorization;

use App\Core\Application\Lock\Authorization\LockStealAuthorizationService;
use App\Tests\_Setup\Builder\CurrentUserContextBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LockStealAuthorizationService.
 *
 * RBAC Rule: Doctors (st_sign=true) can steal locks from secretaries (st_sign=false).
 * Same-tier or reverse direction is NOT allowed.
 *
 * @group lock
 * @group unit
 */
final class LockStealAuthorizationServiceTest extends TestCase
{
    private LockStealAuthorizationService $service;

    protected function setUp(): void
    {
        $this->service = new LockStealAuthorizationService();
    }

    // =========================================================================
    // CORE BUSINESS RULES
    // =========================================================================

    public function test_doctor_can_steal_from_secretary(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withUsername('DR_SMITH')
            ->withStaffCapabilities(['st_sign' => true])
            ->build();

        $result = $this->service->canStealFrom($doctor, ['st_sign' => false]);

        $this->assertTrue($result, 'Doctor should be able to steal from secretary');
    }

    public function test_secretary_cannot_steal_from_doctor(): void
    {
        $secretary = CurrentUserContextBuilder::create()
            ->withUsername('SECRETARY_JONES')
            ->withStaffCapabilities(['st_sign' => false])
            ->build();

        $result = $this->service->canStealFrom($secretary, ['st_sign' => true]);

        $this->assertFalse($result, 'Secretary should NOT be able to steal from doctor');
    }

    public function test_doctor_cannot_steal_from_doctor(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withUsername('DR_ALPHA')
            ->withStaffCapabilities(['st_sign' => true])
            ->build();

        $result = $this->service->canStealFrom($doctor, ['st_sign' => true]);

        $this->assertFalse($result, 'Doctor should NOT steal from another doctor');
    }

    public function test_secretary_cannot_steal_from_secretary(): void
    {
        $secretary = CurrentUserContextBuilder::create()
            ->withUsername('SECRETARY_A')
            ->withStaffCapabilities(['st_sign' => false])
            ->build();

        $result = $this->service->canStealFrom($secretary, ['st_sign' => false]);

        $this->assertFalse($result, 'Secretary should NOT steal from another secretary');
    }

    // =========================================================================
    // EDGE CASES - DISCOVERED VIA TDD
    // =========================================================================

    public function test_handles_numeric_st_sign_from_legacy_db(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withStaffCapabilities(['st_sign' => 1])
            ->build();

        $result = $this->service->canStealFrom($doctor, ['st_sign' => 0]);

        $this->assertTrue($result, 'Should handle integer st_sign from legacy DB');
    }

    public function test_handles_string_st_sign(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withStaffCapabilities(['st_sign' => '1'])
            ->build();

        $result = $this->service->canStealFrom($doctor, ['st_sign' => '0']);

        $this->assertTrue($result, 'Should handle string st_sign');
    }

    public function test_missing_st_sign_treated_as_non_doctor(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withStaffCapabilities(['st_sign' => true])
            ->build();

        $result = $this->service->canStealFrom($doctor, []);

        $this->assertTrue($result, 'Missing st_sign = non-doctor = can be stolen from');
    }

    public function test_requester_without_st_sign_cannot_steal(): void
    {
        $unknownUser = CurrentUserContextBuilder::create()
            ->withStaffCapabilities([])
            ->build();

        $result = $this->service->canStealFrom($unknownUser, ['st_sign' => false]);

        $this->assertFalse($result, 'User without st_sign is not a doctor');
    }

    public function test_null_st_sign_treated_as_non_doctor(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withStaffCapabilities(['st_sign' => true])
            ->build();

        $result = $this->service->canStealFrom($doctor, ['st_sign' => null]);

        $this->assertTrue($result, 'null st_sign = non-doctor');
    }

    // =========================================================================
    // DOCUMENTATION TEST - Other permissions don't affect RBAC
    // =========================================================================

    public function test_other_permissions_do_not_affect_rbac(): void
    {
        $doctor = CurrentUserContextBuilder::create()
            ->withStaffCapabilities([
                'st_sign' => true,
                'st_ope' => true,
                'st_valid' => true,
            ])
            ->build();

        $secretaryWithOtherPerms = [
            'st_sign' => false,
            'st_ope' => true,      // Has st_ope but NOT st_sign
            'st_invoice' => true,
        ];

        $result = $this->service->canStealFrom($doctor, $secretaryWithOtherPerms);

        $this->assertTrue($result, 'Only st_sign determines steal authorization');
    }
}
```

---

## 7. Symfony Integration

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                          │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ POST /api/lock/{type}/steal                              │   │
│  │ StealLockController (Symfony)                            │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                           │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ StealLockHandler                                         │   │
│  │ - Validates request                                      │   │
│  │ - Calls LockStealAuthorizationService                    │   │
│  │ - Executes steal if authorized                           │   │
│  │ - Publishes events                                       │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DOMAIN LAYER                              │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ LockStealAuthorizationService (Pure PHP - TDD)           │   │
│  │                                                          │   │
│  │ Business Rule:                                           │   │
│  │ Doctor (st_sign=true) > Secretary (st_sign=false)        │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

### The Handler (Application Layer)

```php
<?php
// src/Core/Application/Lock/Handler/StealLockHandler.php

declare(strict_types=1);

namespace App\Core\Application\Lock\Handler;

use App\Bridge\Application\CurrentUserContext;
use App\Core\Application\Lock\Authorization\LockStealAuthorizationService;
use App\Core\Application\Lock\Authorization\StaffPermissionLookup;
use App\Core\Application\Lock\LockServiceInterface;
use App\Core\Domain\Lock\Dto\StealLockResultDto;

final readonly class StealLockHandler
{
    public function __construct(
        private LockServiceInterface $lockService,
        private LockStealAuthorizationService $authorizationService,
        private StaffPermissionLookup $permissionLookup,
    ) {}

    public function handle(
        string $lockType,
        string $lockId,
        CurrentUserContext $requester
    ): StealLockResultDto {
        // 1. Get current lock info
        $lockInfo = $this->lockService->getLockInfo($lockType, $lockId);

        if ($lockInfo === null) {
            return StealLockResultDto::notLocked();
        }

        // 2. Check authorization (uses our TDD-tested service!)
        $holderPermissions = $this->permissionLookup->getPermissionsForUser(
            $lockInfo->holderUsername,
            $requester->getCurrentSiteId()
        );

        if (!$this->authorizationService->canStealFrom($requester, $holderPermissions)) {
            return StealLockResultDto::unauthorized(
                'You do not have permission to steal this lock'
            );
        }

        // 3. Execute steal
        $this->lockService->forceRelease($lockType, $lockId);
        $newLock = $this->lockService->acquire($lockType, $lockId, $requester);

        return StealLockResultDto::success($newLock);
    }
}
```

### Symfony Service Configuration

```yaml
# config/services.yaml

services:
    # Auto-wiring handles most of it
    App\Core\Application\Lock\Authorization\LockStealAuthorizationService: ~

    App\Core\Application\Lock\Handler\StealLockHandler:
        arguments:
            $lockService: '@App\Core\Application\Lock\LockServiceInterface'
            $authorizationService: '@App\Core\Application\Lock\Authorization\LockStealAuthorizationService'
            $permissionLookup: '@App\Core\Application\Lock\Authorization\StaffPermissionLookup'
```

### The Controller (Presentation Layer)

```php
<?php
// src/Segur/Infrastructure/Symfony/Controller/Api/StealLockController.php

declare(strict_types=1);

namespace App\Segur\Infrastructure\Symfony\Controller\Api;

use App\Bridge\Application\CurrentUserContext;
use App\Core\Application\Lock\Handler\StealLockHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class StealLockController
{
    public function __construct(
        private readonly StealLockHandler $handler,
    ) {}

    #[Route('/api/lock/{type}/steal', name: 'api_lock_steal', methods: ['POST'])]
    public function __invoke(
        string $type,
        #[CurrentUser] CurrentUserContext $user,
    ): JsonResponse {
        $result = $this->handler->handle($type, $type, $user);

        if (!$result->success) {
            return new JsonResponse(
                ['error' => $result->errorMessage],
                $result->isUnauthorized ? Response::HTTP_FORBIDDEN : Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse([
            'success' => true,
            'lock' => $result->lockInfo->toArray(),
        ]);
    }
}
```

### Testing Strategy by Layer

| Layer | Test Type | Dependencies |
|-------|-----------|--------------|
| Domain (AuthorizationService) | **Unit tests** | None (pure PHP) |
| Application (StealLockHandler) | Integration tests | Real services |
| Presentation (Controller) | Functional tests | Full stack |

```bash
# Run only unit tests (fast, TDD)
./vendor/bin/phpunit tests/Unit/

# Run integration tests
./vendor/bin/phpunit tests/Integration/

# Run all tests
make test
```

---

## 8. Key Takeaways

### TDD Benefits Demonstrated

| Benefit | How We Saw It |
|---------|---------------|
| **Design feedback** | Simple API emerged naturally |
| **Documentation** | Tests explain business rules |
| **Edge case discovery** | Found legacy DB type issues |
| **Regression protection** | All cases covered forever |
| **Confidence to refactor** | Can change implementation safely |

### The TDD Rhythm

```
         ┌──────────────────┐
         │                  │
    ┌────▼────┐        ┌────┴────┐
    │   RED   │───────▶│  GREEN  │
    │ (Test)  │        │ (Code)  │
    └────┬────┘        └────┬────┘
         │                  │
         │    ┌─────────────┘
         │    │
         │  ┌─▼───────┐
         │  │REFACTOR │
         │  └────┬────┘
         │       │
         └───────┘
```

### Rules We Followed

1. **One test at a time** - Don't write multiple failing tests
2. **Minimal code to pass** - Don't anticipate future needs
3. **Refactor only when green** - Tests protect us during cleanup
4. **Test behavior, not implementation** - `canStealFrom()` not `isDoctor()`
5. **No mocks for domain objects** - Use Builders instead

### The "Mocks Lie" Philosophy

```php
// ❌ Mock - simulates behavior, might diverge from reality
$mock = $this->createMock(CurrentUserContext::class);
$mock->method('getStaffCapabilities')->willReturn([...]);

// ✅ Builder - creates real objects with controlled state
$real = CurrentUserContextBuilder::create()
    ->withStaffCapabilities([...])
    ->build();
```

### When to Use TDD

| Good for TDD | Not Ideal for TDD |
|--------------|-------------------|
| Business rules | CRUD operations |
| Algorithms | UI/Templates |
| Value Objects | Configuration |
| State machines | Migrations |
| Calculations | Infrastructure setup |

---

## Summary

This case study showed how TDD was applied to a **real production feature** in a legacy medical system:

1. **Started with the simplest test** (doctor steals from secretary)
2. **Added negative test** to force real implementation
3. **Discovered edge cases** through test-writing
4. **Used Builder pattern** instead of mocks
5. **Integrated with Symfony** without contaminating domain logic

The result: **10 tests** that document business rules, catch regressions, and give confidence to refactor.

---

**Next**: Return to [README](../README.md) or explore other branches.
