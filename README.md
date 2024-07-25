# Online Banking

## Index

- [About](#about)
    - [Dictionary](#dictionary)
- [Usage](#usage)
    - [Installation](#installation)
    - [Running application](#running-application)
    - [Development](#development)
- [Decisions record](#decisions-record)


## About
This is a training web-banking application (backend API) allowing its users to manage their funds inside the system.

### Dictionary

#### *User*
A person registered in the system, identified by email and performing transactions in the system.

#### *Account*
Like a wallet, stores user's money balance state.

#### *Sender*
User from who's account money are withdrawn during transfer.

#### *Recipient*
User to who's account money are deposited during transfer. 

#### *Transfer*
Operation of withdrawing money from sender's account and depositing them to recipient's account.

#### *Subscription*
Recurring withdrawal of money (fee) from user's account on periodic basis (monthly, yearly).

## Usage

### Requirements
- Docker Desktop

### Installation
- Clone the repository to your local machine:
```
$ git clone https://github.com/VladimirShevchuk/online-banking.git
```
- Install the required dependencies:
```
$ composer install
```

- Create .env config file:
```
$ cp .env.example .env
```

- Start the application's docker containers:
```
$ ./vendor/bin/sail up
```

- Run migrations to initialize the database:
```
$ ./vendor/bin/sail artisan migrate --seed
```

### Running application

#### Start
- Start Docker environment with all components:
```
$ ./vendor/bin/sail up -d
```
Option `-d` allows to run the process in background.

- Open a new or in the same CLI tab start Laravel Horizon to start queues:
```
$ ./vendor/bin/sail artisan horizon
```

#### Stop
```
$ ./vendor/bin/sail stop
```

#### Calling APIs
- Basic API documentation can be found under http://127.0.0.1/api/documentation.
- Postman collection file named `online-banking.postman_collection.json` can be found in the root folder of the project. Import it in Postman to start making API calls.
- API is protected by basic auth. Out of the box, system contains 2 users `sender@example.com` and `recipient@example.com` with password `password` (Postman collection uses sender@example.com).

#### Running recurring jobs
- In a new CLI tab run the CRON scheduler:
```
$ ./vendor/bin/sail artisan schedule:work
```

### Development

#### Check code styling compatibility (run without `--test` option to fix styling issues):
```
$ ./vendor/bin/sail pint app --test 
```
#### Refresh API documentation:
```
$ ./vendor/bin/sail artisan l5-swagger:generate
```

## Decisions record
### DR 1: Use client-generated reference_id in the Transfer creation request
**Benefits**: This attribute will help to support idempotency and prevent creation of the same transfer multiple times

### DR 2: Use cents in the amount and price fields in requests body and databse
**Benefits**: Using cents will help to avoid complexity, errors of calculation results rounding and system-dependent formats like float.

### DR 3: Use jobs and queues for async processing of incoming Transfers and recurring fees
**Benefits**: This approach reduces user's wait time, provides better throughput, scalability and resiliency.

### DR 4: Use database native "SELECT FOR UPDATE" mechanism for data locking during changes
**Benefits**: Using database's native functionality, we don't need to implement and support complex logic of locking mechanism, dealing with errors during lock/unlock and concurrent requests.
**Drawbacks**: Because of Eloquent's Active Record approach and implementation details, code dealing with locks is less "clean". It can cause higher memory usage and extra-effort coding around models in some specific use-cases in future.

This approach was selected because of speed of development.

### DR 5: Use automatic audit log data to show account statement
**Benefits**: Less error-prone, cause developers don't need to manually create a record about balance change in each place in the code (which they can forget to do). More reliability.
**Drawbacks**: Automatic audit log record has no data about Transfer ID for balance change. If this data needed, then manual or aspect-oriented audit log creation is beneficial.

Since there was no specific requirement, for simplicity this approach was selected.
