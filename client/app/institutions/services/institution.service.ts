import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    createInstitutionMutation,
    deleteInstitutionsMutation,
    institutionQuery,
    institutionsQuery,
    updateInstitutionMutation,
} from './institutionQueries';
import 'rxjs/add/observable/of';
import {
    CreateInstitutionMutation,
    DeleteInstitutionsMutation,
    InstitutionQuery,
    InstitutionsQuery,
    UpdateInstitutionMutation,
} from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';

@Injectable()
export class InstitutionService
    extends AbstractModelService<InstitutionQuery['institution'],
        InstitutionsQuery['institutions'],
        CreateInstitutionMutation['createInstitution'],
        UpdateInstitutionMutation['updateInstitution'],
        DeleteInstitutionsMutation['deleteInstitutions']> {

    constructor(apollo: Apollo) {
        super(apollo,
            'institution',
            institutionQuery,
            institutionsQuery,
            createInstitutionMutation,
            updateInstitutionMutation,
            deleteInstitutionsMutation);
    }

    public getEmptyObject() {
        return {
            name: '',
        };
    }

}
