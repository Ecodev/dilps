import {
    NaturalSearchConfiguration,
    TypeNumericRangeComponent,
    TypeNumericComponent,
    TypeSelectComponent,
} from '@ecodev/natural-search';
import { CardVisibility } from './generated-types';

function yearToJulian(year: number, endOfYear: boolean): number {
    const date = new Date(year, endOfYear ? 11 : 0, endOfYear ? 31 : 1);

    return Math.trunc(date.getTime() / 86400000 + 2440587.5);
}

export const cardsConfiguration: NaturalSearchConfiguration = [
    {
        display: 'Institution',
        attribute: 'locality.name',
    },
    {
        display: 'Datation',
        attribute: 'datings.from-to',
        component: TypeNumericRangeComponent,
        configuration: {
            transformValue: (v) => {
                return {from: yearToJulian(v.from, false), to: yearToJulian(v.to, true)};
            },
        },
    },
    {
        display: 'Datation pour les geeks en Julian',
        attribute: 'datings.from-to',
        component: TypeNumericRangeComponent,
        configuration: {},
    },
    {
        display: 'Artiste',
        attribute: 'artists.name',
    },
    {
        display: 'Institution',
        attribute: 'institution.name',
    },
    {
        display: 'Visibilité',
        attribute: 'visibility',
        component: TypeSelectComponent,
        configuration: {
            items: [
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
                {
                    value: CardVisibility.public,
                    text: 'par tout le monde',
                    color: 'primary',
                },
            ],
            multiple: true,
            displayWith: (item) => item.text,
            matchItems: (a, b) => a.value === b.value,
        },
    },
];
