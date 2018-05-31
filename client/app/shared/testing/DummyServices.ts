import { BehaviorSubject } from 'rxjs';

export class DummyServices {

    public getOne() {
        return [];
    }

    public getAll() {
        return new BehaviorSubject<any>({
            items: [],
            length: 0,
            pageSize: 10,
        });
    }

    public watchAll() {
        return new BehaviorSubject<any>(null);
    }

    public watchOne() {
        return new BehaviorSubject<any>(null);
    }

    public resolve() {
        return new BehaviorSubject<any>(null);
    }

}
