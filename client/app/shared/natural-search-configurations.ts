import {
    NaturalSearchConfiguration,
    TypeNumericRangeComponent,
    TypeNumericComponent,
    TypeSelectComponent,
    Selection,
} from '@ecodev/natural-search';
import { CardVisibility } from './generated-types';

function yearToJulian(year: number, endOfYear: boolean): number {
    const date = new Date(year, endOfYear ? 11 : 0, endOfYear ? 31 : 1);

    return Math.trunc(date.getTime() / 86400000 + 2440587.5);
}

function wrapLike(s: Selection): Selection {
    s.condition.like.value = '%' + s.condition.like.value + '%';
    return s;
}

export const cardsConfiguration: NaturalSearchConfiguration = [
    {
        display: 'Titre',
        field: 'name',
        transform: wrapLike,
    },
    {
        display: 'Titre étendu',
        field: 'expandedName',
        transform: wrapLike,
    },
    {
        display: 'Artistes',
        field: 'artists.name',
        transform: wrapLike,
    },
    {
        display: 'Auteur technique',
        field: 'techniqueAuthor',
        transform: wrapLike,
    },
    {
        display: 'Supplément',
        field: 'addition',
        transform: wrapLike,
    },
    {
        display: 'Datation',
        field: 'datings.from-to',
        component: TypeNumericRangeComponent,
        transform: (s: Selection): Selection => {
            s.condition.between.from = yearToJulian(s.condition.between.from as number, false);
            s.condition.between.to = yearToJulian(s.condition.between.to as number, true);
            return s;
        },
    },
    {
        display: 'Technique',
        field: 'technique',
        transform: wrapLike,
    },
    {
        display: 'Matériel',
        field: 'material',
        transform: wrapLike,
    },
    {
        display: 'Institution',
        field: 'institution.name',
        transform: wrapLike,
    },
    {
        display: 'Localité',
        field: 'locality.name',
        transform: wrapLike,
    },

    {
        display: 'Source',
        field: 'literature',
        transform: wrapLike,
    },
    {
        display: 'Page',
        field: 'page',
        transform: wrapLike,
    },
    {
        display: 'Figure',
        field: 'figure',
        transform: wrapLike,
    },

    {
        display: 'Planche',
        field: 'table',
        transform: wrapLike,
    },

    {
        display: 'ISBN',
        field: 'isbn',
        transform: wrapLike,
    },

    {
        display: 'Droits d\'auteur',
        field: 'rights',
        transform: wrapLike,
    },
    {
        display: 'Visibilité',
        field: 'visibility',
        component: TypeSelectComponent,
        configuration: {
            items: [
                {
                    id: CardVisibility.private,
                    name: 'par moi',
                },
                {
                    id: CardVisibility.member,
                    name: 'par les membres',
                },
                {
                    id: CardVisibility.public,
                    name: 'par tout le monde',
                },
            ],
            multiple: true,
        },
    },
];
