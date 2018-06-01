import { TestBed, inject } from '@angular/core/testing';
import { CardFilter, CardVisibility } from '../generated-types';
import { cardsConfiguration } from '../natural-search-configurations';
import { NaturalFilterService } from './natural-filter.service';
import { NaturalSearchValues } from '@ecodev/natural-search';

fdescribe('NaturalFilterService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            providers: [NaturalFilterService],
        });
    });

    const input: NaturalSearchValues = [
        [
            {
                attribute: 'search',
                value: 'foo',
            },
            {
                attribute: 'artists.name',
                value: 'bar',
            },
            {
                attribute: 'datings.from-to',
                value: {
                    from: 1900,
                    to: 2000,
                },
            },
            {
                attribute: 'visibility',
                value: [
                    {
                        value: CardVisibility.private,
                        text: 'par moi',
                        color: null,
                    },
                    {
                        value: CardVisibility.member,
                        text: 'par les membres',
                        color: 'accent',
                    },
                ],
            },
        ],
    ];

    const expected: CardFilter = {
        joins: {
            artists: {
                filter: {
                    conditions: [{
                        fields: {
                            name: {like: {value: '%bar%'}},
                        },
                    }],
                },
            },
            datings: {
                filter: {
                    conditions: [{
                        fields: {
                            from: {greaterOrEqual: {value: 2415020}},
                            to: {lessOrEqual: {value: 2451909}},
                        },
                    }],
                },
            },
        },
        conditions: [{
            fields: {
                custom: {search: {value: 'foo'}},
                visibility: {in: {values: [CardVisibility.private, CardVisibility.member]}},
            },
        }],
    };

    it('should be created', inject([NaturalFilterService], (service: NaturalFilterService) => {
        expect(service).toBeTruthy();
    }));

    it('should be created', inject([NaturalFilterService], (service: NaturalFilterService) => {
        expect(service.cards(cardsConfiguration, input)).toEqual(expected as any);
    }));
});
