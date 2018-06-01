import { Injectable } from '@angular/core';
import { isString } from 'lodash';
import {
    NaturalSearchValues,
    NaturalSearchConfiguration,
    NaturalSearchItemConfiguration,
    TypeNumericRangeComponent,
    TypeSelectComponent,
} from '@ecodev/natural-search';
import {
    ArtistFilterJoins,
    CardFilterJoins,
    ChangeFilterJoins,
    CollectionFilterJoins,
    CountryFilterJoins,
    DatingFilterJoins,
    InstitutionFilterJoins,
    TagFilterJoins,
    UserFilterJoins,
    ArtistFilter,
    CardFilter,
    ChangeFilter,
    CollectionFilter,
    CountryFilter,
    DatingFilter,
    InstitutionFilter,
    TagFilter,
    UserFilter,
    ArtistFilterConditionFields,
    CardFilterConditionFields,
    ChangeFilterConditionFields,
    CollectionFilterConditionFields,
    CountryFilterConditionFields,
    DatingFilterConditionFields,
    InstitutionFilterConditionFields,
    TagFilterConditionFields,
    UserFilterConditionFields,
} from '../generated-types';

type Joins =
    CardFilterJoins
    & UserFilterJoins
    & InstitutionFilterJoins
    & CountryFilterJoins
    & CollectionFilterJoins
    & ArtistFilterJoins
    & TagFilterJoins
    & DatingFilterJoins
    & ChangeFilterJoins;

type Filter =
    CardFilter
    & UserFilter
    & InstitutionFilter
    & CountryFilter
    & CollectionFilter
    & ArtistFilter
    & TagFilter
    & DatingFilter
    & ChangeFilter;

type FilterConditionFields =
    CardFilterConditionFields
    & UserFilterConditionFields
    & InstitutionFilterConditionFields
    & CountryFilterConditionFields
    & CollectionFilterConditionFields
    & ArtistFilterConditionFields
    & TagFilterConditionFields
    & DatingFilterConditionFields
    & ChangeFilterConditionFields;

@Injectable({
    providedIn: 'root',
})
export class NaturalFilterService {

    cards(configuration: NaturalSearchConfiguration, values: NaturalSearchValues): Filter {

        const result: Filter = {joins: {}, conditions: [{fields: {}}]};
        const fields: FilterConditionFields = result.conditions[0].fields;
        const joins: Joins = result.joins;

        for (const group of values) {
            for (const naturalValue of group) {
                const field = naturalValue.attribute;
                const value = naturalValue.value;
                const component = this.getComponent(configuration, field);

                this.apply(component, joins, fields, field, value);
            }
        }

        console.log(result.joins);
        console.log(result.conditions[0].fields);

        return result;
    }

    private apply(component, joins: Joins, fields: FilterConditionFields, field: string, value: any): void {

        // Apply join, then apply operator on that join, if field name has a '.'
        const parts = field.split('.');
        if (parts.length > 1) {
            joins[parts[0]] = {filter: {conditions: [{fields: {}}]}};
            fields = joins[parts[0]].filter.conditions[0].fields;
            field = parts[1];
        }

        this.applyOperator(component, fields, field, value);
    }

    private applyOperator(component, fields: FilterConditionFields, field: string, value: any): void {
        if (field === 'search') {
            fields.custom = {search: {value: value}};
        } else if (field === 'from-to') {
            fields.from = {greaterOrEqual: {value: this.yearToJulian(value.from, false)}};
            fields.to = {lessOrEqual: {value: this.yearToJulian(value.to, true)}};
        } else if (component === TypeNumericRangeComponent) {
            fields[field] = {in: {values: value.map(v => v.value)}};
        } else if (component === TypeSelectComponent) {
            fields[field] = {in: {values: value.map(v => v.value)}};
        } else if (isString(value)) {
            fields[field] = {like: {value: '%' + value + '%'}};
        }
    }

    private getComponent(configuration: NaturalSearchConfiguration, field: string) {
        const a = configuration.find(v => v.attribute === field);

        return a ? a.component : null;
    }

    private yearToJulian(year: number, endOfYear: boolean): number {
        const date = new Date(year, endOfYear ? 11 : 0, endOfYear ? 31 : 1);

        return Math.trunc(date.getTime() / 86400000 + 2440587.5);
    }

}
