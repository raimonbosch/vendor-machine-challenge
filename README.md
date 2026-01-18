# Vending Machine Coding Challenge

The goal of this program is to model a vending machine and the state it must maintain during its operation. How exactly the actions on the machine are driven is left intentionally vague and is up to the candidate

The machine works like all vending machines: it takes money then gives you items. The vending machine accepts money in the form of 0.05, 0.10, 0.25 and 1

You must have at least have 3 primary items that cost 0.65, 1.00, and 1.50. Also user may hit the button “return coin” to get back the money they’ve entered so far, If you put more money in than the item price, you get the item and change back.

## How to run

To install all the docker build:

> make install

To run individual commands:
 
> make run EXPR="1, 0.25, 0.25, GET-SODA"
> 
> make run EXPR="0.10, 0.10, RETURN-COIN"
>
> make run EXPR="1, GET-WATER"

To run via an interactive shell:

> make run-interactive

To run a set of prepared tests:

> make test

## Solution

![Vending Machine Callenge](https://github.com/user-attachments/assets/64d1fcda-7921-4966-b13f-bb7395d48c84)

The solution is based on having two `CoinRepository`, one with the immediate coins that the user has put into the vending machine (user coins), another with all the rest of coins (cashier coins). This way the RETURN-COIN logic would be quite direct, since we only have to empty whatever coins we already have there.

The second business class logic which is used by both coin repositories is the `CoinAllocationService`. This class is responsible for deducting a set of coins given an amount. The algorithm will sort the coins from larger to shorter and apply a O(n^2) route in order to get the larger set of coins that match the amount requested.

In general, if the user has given exact change all the logic will be handled by the user coins `CoinRepository`, but in case that we cannot have the exact amount, the calculation of the change to return will be withdrawn from the cashier coins `CoinRepository`. On both cases at the end all the coins from the user coins are passed to the cashier coins. Also, the product requested is deducted from the `ProductRepository`.

As you may have seen we are using mostly **DDD (Domain Driven Design)** patterns to do all. So we start the logic by an Application use case `VendingMachineUseCase`, from there we depend on two more application services (`VendingMachineService` and `VendingMachineParserService`), and those generate domain dependencies with the repositories (that have its own infra implementations). The basic value objects from the application are `Coin`, `Action` and `Product`. Furthermore, we have a set of Domain exceptions thrown during the domain logic so the handling of those are usually done on the application layer. Finally, from the infra point of view, we use docker, PHP 8.4, composer and CodeIgniter4. Also, we added a set of tests via `npm test`, so we have a set not dependent on PHP.

## Requirements

Docker should be enough as long as you already have a way to run Makefiles.

## Specification

### Valid set of actions on the vending machine are:

* 0.05, 0.10, 0.25, 1 - insert money
* Return Coin - returns all inserted money
* GET Water, GET Juice, GET Soda - select item (Water = 0.65, Juice = 1.00, Soda = 1.50)
* SERVICE - a service person opens the machine and set the available change and how many items we have.

### Valid set of responses on the vending machine are:

* 0.05, 0.10, 0.25 - return coin
* Water,  Juice, Soda - vend item

### Vending machine must track the following state:

* Available items - each item has a count, a price and selector
* Available change - Number os coins available
* Currently inserted money

## Examples
```
Example 1: Buy Soda with exact change
1, 0.25, 0.25, GET-SODA
-> SODA

Example 2: Start adding money, but user ask for return coin
0.10, 0.10, RETURN-COIN
-> 0.10, 0.10

Example 3: Buy Water without exact change
1, GET-WATER
-> WATER, 0.25, 0.10
```

# Considerations
* Programming language should be *PHP*
* Solution with `Dockerfile` or `docker-compose` is highly appreciated
* When you finish,  why not go to an extra mille and add some tests? :)

# Additional Notes
* The provided solutions needs to be uploaded into a public repository (Github, Gitlab, bitbucket) with a README.MD providing the following information.
    * Instructions on how to run your solution
    * Requirements
* Please make sure the name **YOUR_COMPANY** are not referenced in any place in your code.
* Commit from the very beginning and commit often. We value the possibility to review your git log.
