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

    const action = {__typename: 'Action'};
    const document = {__typename: 'Document'};
    const company = {__typename: 'Company'};

    const expectAction = {
        id: '456',
        __typename: 'Action',
    };

    const expectedLink = {
        data: {
            linkActionDocument: expectAction,
        },
    };

    const expectedUnlink = {
        data: {
            unlinkActionDocument: expectAction,
        },
    };

    it('should be able to link', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.link(action, document).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedLink);
    })));

    it('should be able to link in reverse order', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.link(document, action).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedLink);
    })));

    it('should be able to unlink', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.unlink(action, document).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedUnlink);
    })));

    it('should be able to unlink in reverse order', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        let actual = null;
        tick();
        service.unlink(document, action).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expectedUnlink);
    })));

    it('should throw for non-existing link mutation', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        tick();
        expect(() => service.link(action, company).subscribe()).toThrowError('API does not allow to link Company and Action');
        tick();
    })));

    it('should throw for non-existing unlink mutation', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {
        tick();
        expect(() => service.unlink(action, company).subscribe()).toThrowError('API does not allow to unlink Company and Action');
        tick();
    })));

    it('should be able to composed name models', fakeAsync(inject([LinkMutationService], (service: LinkMutationService) => {

        const groupDocument = {__typename: 'GroupDocument'};
        const taxonomy = {__typename: 'Taxonomy'};
        const expected = {
            data: {
                linkGroupDocumentTaxonomy: {
                    id: '456',
                    __typename: 'GroupDocument',
                },
            },
        };

        let actual = null;
        tick();
        service.link(groupDocument, taxonomy).subscribe(v => actual = v);
        tick();

        expect(actual).toEqual(expected);
    })));
});
