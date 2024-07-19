describe('Creating New App', () => {
  it('should create a new app with valid details', () => {    
    cy.fixture('CreateAppDetails.json').then((credentials) => { 
      const { username, password, applicationName, description, hosting, addengineurlendpoint, type, filename } = credentials
      cy.visit('http://localhost:8888/apimaker/login')
      cy.get('.form-control.form-control-sm[placeholder="UserName"]').type(username) // Username
      cy.get('.form-control.form-control-sm[placeholder="Password"]').type(password) // Password
      cy.get('.btn').click({force:true}).wait(1000) // Login button
      cy.window().then(win => { // Creating a custom dialog box to wait until the captcha fill
        const name = prompt('Please Enter Your Captcha')
        cy.get('input[placeholder="Captcha Code"]').clear().type(name)
      })                
      // cy.get('.form-control.form-control-sm[placeholder="Captcha Code"]').type('223366') // Bypassing Captcha field
      cy.get('.btn').click({force:true}).wait(1000) // Login button   

// Creating App :-
      cy.contains('Create App').click({force:true}) // Create App Button
      const typeApplicationNameAndWait = (applicationName) => {
      cy.get('input.form-control.form-control-sm').clear().wait(500).type(applicationName).wait(500) // Application Name
      }
      typeApplicationNameAndWait(applicationName)
      cy.get('input.form-control.form-control-sm', { timeout: 5000 }).then(() => {
        if (!applicationName || applicationName.trim().length === 0 || applicationName.length < 4 || applicationName.length > 25 || !/^[a-z]+$/.test(applicationName)) {
          cy.get('.btn.btn-danger.btn-sm').click({force:true})
          cy.get('input.form-control.form-control-sm').should('have.value', '').should('have.class', 'border-danger')
          cy.log('Invalid Application Name')
        } else {
          cy.log('Application name is valid')
        }
      }); 
      const typeDescriptionAndWait = (description) => {        
        cy.get('textarea.form-control.form-control-sm').clear().wait(1000).type(description).wait(500); // Description
      }
      typeDescriptionAndWait(description)    
      cy.get('textarea.form-control.form-control-sm', { timeout: 5000 }).then(() => {
        if (!description || !description.trim().length === 0 || description.length < 4 || description.length > 50 || !/^[-_.,a-zA-Z\s]*$/.test(description)) {                    
          cy.get('.btn.btn-danger.btn-sm').click({force:true})
          cy.get('textarea.form-control.form-control-sm').should('have.class', 'border-danger')
          cy.log('Invalid Description')          
        } else {          
          cy.log('Description is valid')
        }
      })       
      cy.get('.btn.btn-danger.btn-sm').click({force:true}).wait(1000) // Create     
      
      cy.document().then((doc) => { 
        const alertElement = doc.querySelector('.alert.alert-danger')
        if (alertElement) {
          cy.log("Application already exists")
          cy.get('.alert.alert-danger').should('be.visible').and('contain', 'Already exists') // Assertion check for duplication          
        } else {
          cy.log('Successfully Submitted & Alert message element not found')
          cy.xpath(`//div[contains(@style, "height: calc(100% - 100px)")]//a[contains(.,'${applicationName}')]`).click({force:true}).wait(1000) // Opening Newly Created one
          cy.get('h5.d-inline').should('have.text', `${applicationName} Settings`)
          if(hosting == "Local Host"){
            cy.get('input[value="UPDATE"]').eq(0).click() // Cloud Hosting Update button
            cy.get('input[value="UPDATE"]').eq(1).click() // Cloud Hosting Update button
            cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(0).select(type) // Type : file dropdown at other settings
            cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(1).select(filename) // file name selection dropdown
            cy.get('input[value="UPDATE"]').eq(2).click() // Update button
          }
          else if(hosting == "Cloud Hosting"){              
            cy.get('label[style="cursor: pointer;"] input[type="checkbox"]').eq(0).check() // Enable Cloud Hosting                       
            cy.get('input.form-control.form-control-sm[placeholder="SubDomain"]').clear().type(applicationName) // SubDomain name after https://  
            // Pending "USE an alias name for above domain" check box clicking and name of the alias url         
            cy.get('input[value="UPDATE"]').eq(0).click() // Update button
          }else if(hosting == "Custom Hosting"){
            cy.get('label[style="cursor: pointer;"] input[type="checkbox"]').eq(1).check() // Enable Custom Hosting                       
            cy.get('input.form-control.form-control-sm').eq(1).clear().type(`http://www.${applicationName}.com/path/`); // Urls with path where engine is configured        
            cy.get('input[value="UPDATE"]').eq(1).click() // Update button
            if(addengineurlendpoint){
              cy.get('input[type="button"].btn.btn-outline-dark.btn-sm[value="Add Engine URL Endpoint"]').click({force:true});
              cy.get('input.form-control.form-control-sm').eq(2).clear().type(`http://www.${addengineurlendpoint}.com/path/`);
              cy.get('input[value="UPDATE"]').eq(1).click() // Update button
            }else{
              cy.log('No Engine URL Added')
            }
          // Pending Access key process
          }else if(hosting == "Other Settings"){
            if(type == 'file'){
              cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(0).select(type) // File selecting
              cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(1).select(filename) // Choosing file name
              cy.get('input[value="UPDATE"]').eq(2).click() // Update button
            }
            else if(type == 'page'){
              cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(0).select(type) // File selecting
              cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(1).select(filename) // Choosing file name
              cy.get('input[value="UPDATE"]').eq(2).click() // Update button
            }else if(type == 'Api Summary'){
              cy.get('.form-select.form-select-sm.w-auto.d-inline').eq(0).select(type) // File selecting           
              cy.get('input[value="UPDATE"]').eq(2).click() // Update button
            }else{
              cy.log("No type given please check once.") 
            }
          }
        }
      });      
      cy.xpath(`//a[text()='APPs']`, { multiple: true }).click({force:true}).wait(1000) // APPs home page.

// Deleting App      
      cy.xpath(`//div[contains(@style, "height: calc(100% - 100px)")]//a[contains(., '${applicationName}')]`).should('exist').wait(1000)// Checking Appname Exist or Not            
      cy.contains('div', applicationName).parent().within(() => {        
        cy.get('.btn-outline-danger').click(); // Clicking the X button
      });      
    });  
  });
});      