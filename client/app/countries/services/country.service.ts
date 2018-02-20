import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { countryQuery, countriesQuery } from './countryQueries';
import 'rxjs/add/observable/of';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { CountriesQuery, CountryQuery } from '../../shared/generated-types';

@Injectable()
export class CountryService
    extends AbstractModelService<CountryQuery['country'],
        CountriesQuery['countries'],
        null,
        null,
        null> {

    constructor(apollo: Apollo) {
        super(apollo,
            'country',
            countryQuery,
            countriesQuery,
            null,
            null,
            null);
    }

}
