describe('Test in frontend that the users remind view', () => {
  it('can send a reminder email for a test user in a menu item', () => {
    cy.db_createUser({ name: 'test user', email: 'test@example.com' })
      .then(() => cy.db_createMenuItem({ title: 'Automated test reminder', link: 'index.php?option=com_users&view=remind' }))
      .then(() => {
        cy.visit('/');
        cy.get('a:contains(Automated test reminder)').click();
        cy.get('#jform_email').type('test@example.com');
        cy.get('.controls > .btn').click();

        cy.get('#system-message-container').should('contain.text', 'If the email address you entered is registered on this site you will shortly receive an email with a reminder.');
      });
  });

  it('can send a reminder email for a test user without a menu item', () => {
    cy.db_createUser({ name: 'test user', email: 'test@example.com' })
      .then(() => {
        cy.visit('index.php?option=com_users&view=remind');
        cy.get('#jform_email').type('test@example.com');
        cy.get('.controls > .btn').click();

        cy.get('#system-message-container').should('contain.text', 'If the email address you entered is registered on this site you will shortly receive an email with a reminder.');
      });
  });
});
