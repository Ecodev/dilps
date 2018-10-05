import { LoginPage } from './login.po';

describe('Login page', () => {
    let page: LoginPage;

    const wrongCredentias = {
        username: 'wrongname',
        password: 'wrongpasswd'
    };

    const goodCredentias = {
        username: 'administrator',
        password: 'administrator'
    };

    beforeEach(() => {
        page = new LoginPage();
    });

    it('with wrong credentials should see an error notification', () => {
        page.navigateTo();
        page.fillCredentials(wrongCredentias);
        expect(page.getParagraphText()).toEqual('Veuillez choisir une méthode d\'authentification ou\n' +
            'utiliser le bouton "Accès public"');
        expect(page.getErrorMessage()).toEqual('Le nom d\'utilisateur ou mot de passe est incorrect !');
    });

    it('with good credentials should redirect to the main gallery', () => {
        page.navigateTo();
        page.fillCredentials(goodCredentias);
        page.acceptLicense();
        expect(page.getMenu().isDisplayed());
    });
});
