// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
require('cypress-xpath');
import 'cypress-file-upload';
import './commands'

// Global variable code

Cypress.Commands.add('setGlobalVariable', (key, value) => {
  Cypress.env(key, value);
});

Cypress.Commands.add('getGlobalVariable', (key) => {
  return Cypress.env(key);
});


// let globalVariable;

// Cypress.Commands.add('setGlobalVariable', (value) => {
//   globalVariable = value;
// });

// Cypress.Commands.add('getGlobalVariable', () => {
//   return globalVariable;
// });



// Add this in your support/commands.js file

// Cypress.Commands.add("softAssert", (actual, expected, message) => {
//   cy.log(`Soft Assertion: ${message}`);
//   expect(actual).to.equal(expected, message);
// });

// Cypress.Commands.add("softAssertContains", (haystack, needle, message) => {
//   cy.log(`Soft Assertion: ${message}`);
//   expect(haystack).to.include(needle, message);
// });

