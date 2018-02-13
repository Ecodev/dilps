import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    createInstitutionMutation,
    deleteInstitutionMutation,
    institutionQuery,
    institutionsQuery,
    updateInstitutionMutation,
} from './institutionQueries';
import 'rxjs/add/observable/of';
import {
    CreateInstitutionMutation,
    DeleteInstitutionMutation,
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
        DeleteInstitutionMutation['deleteInstitution']> {

    constructor(apollo: Apollo) {
        super(apollo,
            'institution',
            institutionQuery,
            institutionsQuery,
            createInstitutionMutation,
            updateInstitutionMutation,
            deleteInstitutionMutation);
    }

    public getEmptyObject() {
        return {
            name: '',
        };
    }

}
