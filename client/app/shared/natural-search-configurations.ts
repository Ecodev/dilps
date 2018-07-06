import {
    NaturalSearchConfiguration,
    Selection,
    TypeNumericRangeComponent,
    TypeSelectComponent,
} from '@ecodev/natural-search';
import { CardVisibility } from './generated-types';

function wrapLike(s: Selection): Selection {
    s.condition.like.value = '%' + s.condition.like.value + '%';
    return s;
}

function replaceOperatorByField(s: Selection): Selection {
    const oldOperator = s.condition.like ? 'like' : 'between';

    s.condition[s.field] = s.condition[oldOperator];
    delete s.condition[oldOperator];

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
        field: 'yearRange',
        component: TypeNumericRangeComponent,
        transform: replaceOperatorByField,
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
        display: 'Localité',
        field: 'localityOrInstitutionLocality',
        transform: replaceOperatorByField,
    },
    {
        display: 'Institution',
        field: 'institution.name',
        transform: wrapLike,
    },
    {
        display: 'Source',
        field: 'literature',
        transform: wrapLike,
    },
];
