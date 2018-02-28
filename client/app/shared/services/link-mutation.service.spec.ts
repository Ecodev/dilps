import { inject, TestBed, tick, fakeAsync } from '@angular/core/testing';
import { MockApolloProvider } from '../testing/MockApolloProvider';
import { LinkMutationService } from './link-mutation.service';

describe('LinkMutationService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            providers: [
                LinkMutationService,
                MockApolloProvider,
            ],
        });
    });

    const card = {__typename: 'Card'};
    const collection = {__typename: 'Collection'};
    const company = {__typename: 'Company'};

    const expectCollection = {
        id: '456',
        __typename: 'Collection',
    };

    const expectedLink = {
        data: {
            linkCollectionCard: expectCollection,
        },
    };

    const expectedUnlink = {
        data: {
            unlinkCollectionCard: expectCollection,
        },
    };

    it('should be able to link', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.link(collection, card).subscribe(v => actual = v);
        tick();
        expect(actual).toEqual(expectedLink);
    })));

    it('should be able to link in reverse order', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.link(collection, card).subscribe(v => actual = v);
        tick();
        expect(actual).toEqual(expectedLink);
    })));

    it('should be able to unlink', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.unlink(card, collection).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedUnlink);
    })));

    it('should be able to unlink in reverse order', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.unlink(collection, card).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedUnlink);
    })));

    it('should throw for non-existing link mutation', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        tick();
        expect(() => service.link(card, company).subscribe()).toThrowError('API does not allow to link Company and Card');
        tick();
    })));

    it('should throw for non-existing unlink mutation', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        tick();
        expect(() => service.unlink(card, company).subscribe()).toThrowError('API does not allow to unlink Company and Card');
        tick();
    })));

});
