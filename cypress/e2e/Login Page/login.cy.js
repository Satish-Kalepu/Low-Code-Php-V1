describe('Login Tests', () => {
  
  const baseUrl = 'http://localhost:8888/apimaker/login';
  const correctUsername = 'admin';
  const correctPassword = 'Admin123!@#';  
  const incorrectUsernames = ["user123", "unknown123"]
  const incorrectPasswords = ["password123", "abc123"]
  const incorrectCaptchas = ["3z5t7", "p9k3q"]
  const inbypassCaptchas = ["bypass123", "skipCaptcha"]
  // const incorrectUsernames = ["user123", "unknown123", "fakeUser", "wrongName", "test1234", "nonExistent", "user_not_found", "noSuchUser", "missingUser", "user404"];
  // const incorrectPasswords = ["password123", "abc123", "qwerty", "letmein", "123456", "password1", "admin", "12345678", "welcome", "pass123"];
  // const incorrectCaptchas = ["3z5t7", "p9k3q", "1b8v2", "x4l6m", "7y3n9", "d8w5r", "2c7p4", "m3k9v", "5z2r8", "q7n3w"];
  // const inbypassCaptchas = ["bypass123", "skipCaptcha", "freePass", "override123", "noCaptcha", "passThrough", "bypassCode", "directAccess", "noVerification", "testCaptcha"];



  // Utility function to check for error messages
  function checkErrorMessage() {
    cy.get('body').then($body => {
      if ($body.find('.text-danger').length > 0) {
        cy.get('.text-danger').should('be.visible').then($errorMessage => {
          const errorMessageText = $errorMessage.text();
          cy.log('Error Message: ' + errorMessageText);
        });
      } else {
        cy.log('No error message found');
      }
    });
  }

  // // // Test Case 1: Empty Username and Password
  // // it('should display an error message when login with empty username and password', () => {
  // //   cy.visit(baseUrl);
  // //   cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(' ');
  // //   cy.get('.form-control.form-control-sm[placeholder="Password"]').type(' ');
  // //   cy.get('.btn').click({ force: true }).wait(1000);
  // //   checkErrorMessage();
  // // });

  // // // Test Case 2: Empty Username and Incorrect Passwords
  // // incorrectPasswords.forEach(password => {
  // //   it(`should display an error message when login with empty username and password "${password}"`, () => {
  // //     cy.visit(baseUrl);
  // //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(' ');
  // //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  // //     cy.get('.btn').click({ force: true }).wait(1000);
  // //     checkErrorMessage();
  // //   });
  // // });

  // // // Test Case 3: Incorrect Username and Empty Password
  // // incorrectUsernames.forEach(username => {
  // //   it(`should display an error message when login with username "${username}" and empty password`, () => {
  // //     cy.visit(baseUrl);
  // //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  // //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(' ');
  // //     cy.get('.btn').click({ force: true }).wait(1000);
  // //     checkErrorMessage();
  // //   });
  // // });

  // // // Test Case 4: Correct Username and Empty Password
  // // it('should display an error message when login with correct username and empty password', () => {
  // //   cy.visit(baseUrl);
  // //   cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  // //   cy.get('.form-control.form-control-sm[placeholder="Password"]').type(' ');
  // //   cy.get('.btn').click({ force: true }).wait(1000);
  // //   checkErrorMessage();
  // // });

  // // // Test Case 5: Empty Username and Correct Password
  // // it('should display an error message when login with empty username and correct password', () => {
  // //   cy.visit(baseUrl);
  // //   cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(' ');
  // //   cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  // //   cy.get('.btn').click({ force: true }).wait(1000);
  // //   checkErrorMessage();
  // // });

  // // // Test Case 6: Correct Username and Incorrect Passwords
  // // incorrectPasswords.forEach(password => {
  // //   it(`should display an error message when login with correct username and password "${password}"`, () => {
  // //     cy.visit(baseUrl);
  // //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  // //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  // //     cy.get('.btn').click({ force: true }).wait(1000);
  // //     checkErrorMessage();
  // //   });
  // // });

  // // // Test Case 7: Incorrect Usernames and Correct Password
  // // incorrectUsernames.forEach(username => {
  // //   it(`should display an error message when login with username "${username}" and correct password`, () => {
  // //     cy.visit(baseUrl);
  // //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  // //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  // //     cy.get('.btn').click({ force: true }).wait(1000);
  // //     checkErrorMessage();
  // //   });
  // // });

  // // // Test Case 8: Incorrect Username, Incorrect Password, Empty Captcha
  // // incorrectUsernames.forEach(username => {
  // //   incorrectPasswords.forEach(password => {
  // //     it(`should display an error message when login with username "${username}", password "${password}", and empty captcha`, () => {
  // //       cy.visit(baseUrl);
  // //       cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  // //       cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  // //       cy.get('.btn').click({ force: true }).wait(1000);
  // //       cy.get('.btn').click({ force: true }).wait(1000);
  // //       checkErrorMessage();
  // //     });
  // //   });
  // // });

  // // Test Case 9: Incorrect Username, Incorrect Password, Incorrect Captcha
  // incorrectUsernames.forEach(username => {
  //   incorrectPasswords.forEach(password => {
  //     incorrectCaptchas.forEach(captcha => {
  //       it(`should display an error message when login with username "${username}", password "${password}", and incorrect captcha`, () => {
  //         cy.visit(baseUrl);
  //         cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  //         cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  //         cy.get('.btn').click({ force: true }).wait(2000);
  //         cy.get('input[placeholder="Captcha Code"]').type(captcha);
  //         cy.get('.btn').click({ force: true }).wait(1000);
  //         cy.get('.text-danger').should('contain', 'Incorrect Code');
  //       });
  //     })
  //   });
  // });

  // // Test Case 10: Incorrect Username, Incorrect Password, Correct Captcha
  // incorrectUsernames.forEach(username => {
  //   incorrectPasswords.forEach(password => {
  //     it(`should display an error message when login with username "${username}", password "${password}", and correct captcha`, () => {
  //       cy.visit(baseUrl);
  //       cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  //       cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.window().then(win => { // Creating a custom dialog box to wait until the captcha fill
  //         const name = prompt('Please Enter Your Captcha')
  //         cy.get('input[placeholder="Captcha Code"]').clear().type(name)
  //       })                
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.get('.text-danger').should('contain', 'Username or Password Incorrect');
  //     });
  //   });
  // });

  // // Test Case 11: Incorrect Username, Correct Password, Empty Captcha
  // incorrectUsernames.forEach(username => {
  //   it(`should display an error message when login with username "${username}", correct password "${correctPassword}", and empty captcha`, () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     checkErrorMessage();
  //   });
  // });

  // // Test Case 12: Incorrect Username, Correct Password, Incorrect Captcha
  // incorrectUsernames.forEach(username => {
  //   incorrectCaptchas.forEach(captcha => {
  //     it(`should display an error message when login with username "${username}", correct password, and incorrect captcha`, () => {
  //       cy.visit(baseUrl);
  //       cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  //       cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.get('input[placeholder="Captcha Code"]').type(captcha);
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.get('.text-danger').should('contain', 'Incorrect Code');
  //     });
  //   })
  // });

  // // Test Case 13: Incorrect Username, Correct Password, Correct Captcha
  // incorrectUsernames.forEach(username => {
  //   it(`should display an error message when login with username "${username}", correct password, and correct captcha`, () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.window().then(win => { // Creating a custom dialog box to wait until the captcha fill
  //       const name = prompt('Please Enter Your Captcha')
  //       cy.get('input[placeholder="Captcha Code"]').clear().type(name)
  //     })                
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('.text-danger').should('contain', 'Username or Password Incorrect');
  //   });
  // });

  // // Test Case 14: Correct Username, Incorrect Password, Empty Captcha
  // incorrectPasswords.forEach(password => {
  //   it(`should display an error message when login with correct username, password "${password}", and empty captcha`, () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     checkErrorMessage();
  //   });
  // });

  // // Test Case 15: Correct Username, Incorrect Password, Incorrect Captcha
  // incorrectPasswords.forEach(password => {
  //   incorrectCaptchas.forEach(captcha => {
  //     it(`should display an error message when login with correct username, password "${password}", and incorrect captcha`, () => {
  //       cy.visit(baseUrl);
  //       cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //       cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.get('input[placeholder="Captcha Code"]').type(captcha);
  //       cy.get('.btn').click({ force: true }).wait(1000);
  //       cy.get('.text-danger').should('contain', 'Incorrect Code');
  //     });
  //   })
  // });

  // // Test Case 16: Correct Username, Incorrect Password, Correct Captcha
  // incorrectPasswords.forEach(password => {
  //   it(`should display an error message when login with correct username, password "${password}", and correct captcha`, () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.window().then(win => { // Creating a custom dialog box to wait until the captcha fill
  //       const name = prompt('Please Enter Your Captcha')
  //       cy.get('input[placeholder="Captcha Code"]').clear().type(name)
  //     })                
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('.text-danger').should('contain', 'Username or Password Incorrect');
  //   });
  // });

  // // Test Case 17: Correct Username, Correct Password, Empty Captcha
  // it('should display an error message when login with correct username, correct password, and empty captcha', () => {
  //   cy.visit(baseUrl);
  //   cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //   cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //   cy.get('.btn').click({ force: true }).wait(1000);
  //   cy.get('.btn').click({ force: true }).wait(1000);
  //   checkErrorMessage();
  // });

  // // Test Case 18: Correct Username, Correct Password, Incorrect Captcha
  // incorrectCaptchas.forEach(captcha => {
  //   it('should display an error message when login with correct username, correct password, and incorrect captcha', () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('input[placeholder="Captcha Code"]').type(captcha);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('.text-danger').should('contain', 'Incorrect Code');
  //   });
  // })

  // // Test Case 19: Correct Username, Correct Password, Bypass Captcha
  // inbypassCaptchas.forEach(bypassCaptcha => {
  //   it('should successfully login with correct username, correct password, and bypass captcha', () => {
  //     cy.visit(baseUrl);
  //     cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
  //     cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
  //     cy.get('.btn').click({ force: true }).wait(1000);
  //     cy.get('input[placeholder="Captcha Code"]').clear().type(bypassCaptcha);
  //     cy.get('.btn').click({ force: true }).wait(1000);      
  //   });
  // })

  // Test Case 20: Correct Username, Correct Password, Correct Captcha
  // console.log('test')
  it('should successfully login with correct username, correct password, and correct captcha', () => {
    cy.visit(baseUrl);
    cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(correctUsername);
    cy.get('.form-control.form-control-sm[placeholder="Password"]').type(correctPassword);
    cy.get('.btn').click({ force: true }).wait(1000);
    cy.window().then(win => { // Creating a custom dialog box to wait until the captcha fill
      const name = prompt('Please Enter Your Captcha')
      cy.get('input[placeholder="Captcha Code"]').clear().type(name)
    })                
    cy.get('.btn').click({ force: true }).wait(1000);
    // Add appropriate assertions for successful login
  });
});
