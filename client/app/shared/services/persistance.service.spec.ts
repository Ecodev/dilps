import { inject, TestBed } from '@angular/core/testing';

import { PersistenceService } from './persistence.service';

describe('PersistanceService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            providers: [PersistenceService],
        });
    });

    it('should be created', inject([PersistenceService], (service: PersistenceService) => {
        // expect(service).toBeTruthy();
    }));
});
