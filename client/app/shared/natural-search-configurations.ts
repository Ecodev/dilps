import {
    NaturalSearchConfiguration,
    Selection,
    TypeNumericRangeComponent,
    TypeSelectComponent,
} from '@ecodev/natural-search';
import { CardVisibility } from './generated-types';

function yearToJulian(year: number, endOfYear: boolean): number {
    const date = new Date(year, endOfYear ? 11 : 0, endOfYear ? 31 : 1);

    return Math.trunc(date.getTime() / 86400000 + 2440587.5);
}

function transformDate(s: Selection): Selection {
    s.condition.between.from = yearToJulian(s.condition.between.from as number, false);
    s.condition.between.to = yearToJulian(s.condition.between.to as number, true);
    return s;
}

function wrapLike(s: Selection): Selection {
    s.condition.like.value = '%' + s.condition.like.value + '%';
    return s;
}

function replaceOperatorByField(s: Selection): Selection {
    s.condition[s.field] = s.condition.like;
    delete s.condition.like;

    return s;
}

export const adminConfig: NaturalSearchConfiguration = [
    {
        display: 'Visibilité',
        field: 'visibility',
        component: TypeSelectComponent,
        configuration: {
            items: [
                CardVisibility.public,
                CardVisibility.member,
                CardVisibility.private,
            ],
            multiple: true,
        },
    },
];

export const cardsConfiguration: NaturalSearchConfiguration = [
    {
        display: 'Titre',
        field: 'nameOrExpandedName',
        transform: replaceOperatorByField,
    },
    {
        display: 'Artistes',
        field: 'artistOrTechniqueAuthor',
        transform: replaceOperatorByField,
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
        transform: transformDate,
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
        display: 'Lieu',
        field: 'locality.name',
        transform: wrapLike,
    },
    {
        display: 'Institution',
        field: 'institution.name',
        transform: wrapLike,
    },
    {
        display: 'Localité de l\'institution',
        field: 'institution.locality',
    },
    {
        display: 'Source',
        field: 'literature',
        transform: wrapLike,
    },
];
