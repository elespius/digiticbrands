
afterEach(function() {
  cy.task('queryDB', 'DELETE FROM #__content');
  cy.task('queryDB', 'DELETE FROM #__content_frontpage');
  cy.task('queryDB', 'DELETE FROM #__menu WHERE id > 101');
});

describe('Test that the list view ', () => {
  ['default', 'blog'].forEach((layout) => {
    it('can display a list of articles in the ' + layout + ' layout in a menu item', function () {
      cy.db_createArticle({title: 'article 1'})
          .then(() => cy.db_createArticle({title: 'article 2'}))
          .then(() => cy.db_createArticle({title: 'article 3'}))
          .then(() => cy.db_createArticle({title: 'article 4'}))
          .then(() => cy.db_createMenuItem({'title': 'automated test', link: 'index.php?option=com_content&view=category&id=2&layout=' + layout}))
          .then(() => {
              cy.visit('/');
              cy.get('a:contains(automated test)').click();

              cy.contains('article 1');
              cy.contains('article 2');
              cy.contains('article 3');
              cy.contains('article 4');
          });
    });

    it('can display a list of articles in the ' + layout + ' layout without a menu item', function () {
      cy.db_createArticle({title: 'article 1'})
          .then(() => cy.db_createArticle({title: 'article 2'}))
          .then(() => cy.db_createArticle({title: 'article 3'}))
          .then(() => cy.db_createArticle({title: 'article 4'}))
          .then(() => {
              cy.visit('/index.php?option=com_content&view=category&id=2&layout=' + layout);

              cy.contains('article 1');
              cy.contains('article 2');
              cy.contains('article 3');
              cy.contains('article 4');
          });
    });
  });

  it('can open the article form in the default layout', function () {
    cy.db_createArticle({title: 'article 1'})
      .then(() => cy.db_createMenuItem({'title': 'automated test', link: 'index.php?option=com_content&view=category&id=2&layout=default'}))
      .then(() => {
      cy.doFrontendLogin(Cypress.env('username'), Cypress.env('password'))
      cy.visit('/');
      cy.get('a:contains(automated test)').click();
      cy.get('a:contains(New Article)').click();

      cy.get('#adminForm').should('exist');
    });
  });
});
