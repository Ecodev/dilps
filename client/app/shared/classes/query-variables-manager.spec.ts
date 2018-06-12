import { QueryVariablesManager } from './query-variables-manager';

describe('QueryVariablesManager', () => {

    let manager: QueryVariablesManager;

    beforeEach(() => {
        manager = new QueryVariablesManager();
        // expect(manager.variables.value).toEqual(null);
    });

    it('should merge flat objects as lodash does in same channel', () => {
        manager.merge('a', {a: 1});
        expect(manager.variables.value).toEqual({a: 1});

        manager.merge('a', {a: 2});
        expect(manager.variables.value).toEqual({a: 2});

        manager.merge('a', {b: 3});
        expect(manager.variables.value)
            .toEqual({
                a: 2,
                b: 3,
            });

        manager.merge('a', {b: 4});
        expect(manager.variables.value)
            .toEqual({
                a: 2,
                b: 4,
            });
    });

    it('should merge nested objects as lodash does in same channel', () => {

        manager.merge('a', {a: 1});
        expect(manager.variables.value).toEqual({a: 1});

        manager.merge('a', {a: {aa: 11}});
        expect(manager.variables.value).toEqual({a: {aa: 11}});

        manager.merge('a', {a: {aa: 12}});
        expect(manager.variables.value).toEqual({a: {aa: 12}});

        manager.merge('a', {b: 4});
        expect(manager.variables.value)
            .toEqual({
                a: {aa: 12},
                b: 4,
            });

        manager.merge('a', {a: {b: 13}});
        expect(manager.variables.value)
            .toEqual({
                a: {
                    aa: 12,
                    b: 13,
                },
                b: 4,
            });
    });

    // Same as previous example but always in a different channel
    it('should merge objects as lodash does in multiple channel', () => {

        manager.merge('a', {a: 1});
        expect(manager.variables.value).toEqual({a: 1});

        manager.merge('b', {a: {aa: 11}});
        expect(manager.variables.value).toEqual({a: {aa: 11}});

        manager.merge('c', {a: {aa: 12}});
        expect(manager.variables.value).toEqual({a: {aa: 12}});

        manager.merge('d', {b: 4});
        expect(manager.variables.value)
            .toEqual({
                a: {aa: 12},
                b: 4,
            });

        manager.merge('f', {a: {b: 13}});
        expect(manager.variables.value)
            .toEqual({
                a: {
                    aa: 12,
                    b: 13,
                },
                b: 4,
            });

        // Update an existing channel
        manager.merge('a', {a: {d: 14}});
        expect(manager.variables.value)
            .toEqual({
                a: {
                    aa: 12,
                    b: 13,
                    d: 14,
                },
                b: 4,
            });
    });

    it('should override first found array (from the root) in same channel', () => {

        manager.merge('a',
            {
                myArray: [
                    'a',
                    'b',
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    'a',
                    'b',
                ],
            });

        manager.merge('a',
            {
                myArray: [
                    'c',
                    'd',
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    'c',
                    'd',
                ],
            });

        manager.merge('a',
            {
                myArray: [
                    {a: 1},
                    {b: 2},
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    {a: 1},
                    {b: 2},
                ],
            });

        manager.merge('a', {myArray: [{a: 1}]});
        expect(manager.variables.value).toEqual({myArray: [{a: 1}]});

        manager.merge('a', {myArray: []});
        expect(manager.variables.value).toEqual({myArray: []});

        manager.merge('a', {myArray: [{a: {b: 11}}]});
        expect(manager.variables.value).toEqual({myArray: [{a: {b: 11}}]});

        manager.merge('a',
            {
                myArray: [
                    {
                        a: {
                            b: 12,
                            c: 13,
                        },
                    },
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    {
                        a: {
                            b: 12,
                            c: 13,
                        },
                    },
                ],
            });

        manager.merge('a', {myArray: [{a: {c: 13}}]});
        expect(manager.variables.value).toEqual({myArray: [{a: {c: 13}}]});
    });

    it('should concat first found array (from the root) in different channels', () => {

        manager.merge('a', {myArray: ['a']});
        expect(manager.variables.value).toEqual({myArray: ['a']});

        manager.merge('b', {myArray: [{a: 1}]});
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    'a',
                    {a: 1},
                ],
            });

        manager.merge('c',
            {
                myArray: [
                    {
                        b: [
                            11,
                            12,
                            13,
                        ],
                    },
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    'a',
                    {a: 1},
                    {
                        b: [
                            11,
                            12,
                            13,
                        ],
                    },
                ],
            });

        manager.merge('d',
            {
                myArray: [
                    {
                        b: [
                            14,
                            15,
                            16,
                        ],
                    },
                ],
            });
        expect(manager.variables.value)
            .toEqual({
                myArray: [
                    'a',
                    {a: 1},
                    {
                        b: [
                            11,
                            12,
                            13,
                        ],
                    },
                    {
                        b: [
                            14,
                            15,
                            16,
                        ],
                    },
                ],
            });
    });

    it('should override conditions in graphql typed query variables in same channel', () => {

        const variablesA = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const variablesB = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {e: {equal: 1405}},
                            {f: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {g: {equal: 123}},
                            {g: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        manager.merge('a', variablesA);
        expect(manager.variables.value).toEqual(variablesA);

        manager.merge('a', variablesB);
        expect(manager.variables.value).toEqual(variablesB);
    });

    it('should concat conditions in graphql typed query variables in different channels', () => {

        const variablesA = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const variablesB = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {e: {equal: 1405}},
                            {f: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {g: {equal: 123}},
                            {g: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const result = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                    {
                        fields: [
                            {e: {equal: 1405}},
                            {f: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {g: {equal: 123}},
                            {g: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        manager.merge('a', variablesA);
        expect(manager.variables.value).toEqual(variablesA);

        manager.merge('b', variablesB);
        expect(manager.variables.value).toEqual(result);
    });

    it('should apply context on an existing channel', () => {

        const contextFields = [
            {x: {equal: 'xxxx'}},
            {y: {equal: 'yyyy'}},
        ];

        const variables = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const result = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                ],
            },
        };

        manager.merge('a', variables);
        expect(manager.variables.value).toEqual(variables);

        manager.contextualizeChannel('a', contextFields);
        expect(manager.variables.value).toEqual(result);

    });

    it('should set channel after a context as been setted', () => {

        const contextFields = [
            {x: {equal: 'xxxx'}},
            {y: {equal: 'yyyy'}},
        ];

        const variables = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const result = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                ],
            },
        };


        manager.contextualizeChannel('a', contextFields);
        expect(manager.variables.value).toEqual(null);

        manager.set('a', variables);
        expect(manager.variables.value).toEqual(result);

    });


    it('should update (when called for the first time) a channel after a context as been setted', () => {

        const contextFields = [
            {x: {equal: 'xxxx'}},
            {y: {equal: 'yyyy'}},
        ];

        const variables = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const result = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                ],
            },
        };


        manager.contextualizeChannel('a', contextFields);
        expect(manager.variables.value).toEqual(null);

        manager.merge('a', variables);
        expect(manager.variables.value).toEqual(result);
    });


    it('should apply context on an existing channel and then update channel', () => {

        const contextFields = [
            {x: {equal: 'xxxx'}},
            {y: {equal: 'yyyy'}},
        ];

        const variablesA = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                        ],
                    },
                ],
            },
        };

        const variablesB = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1}},
                            {b: {equal: 2}},
                            {c: {equal: 3}},
                        ],
                    },
                    {
                        fields: [
                            {d: {equal: 4}},
                            {e: {equal: 5}},
                            {f: {equal: 6}},
                        ],
                    },
                ],
            },
        };

        const resultA = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1405}},
                            {b: {equal: 1605}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                    {
                        fields: [
                            {c: {equal: 123}},
                            {d: {equal: 456}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                ],
            },
        };

        const resultB = {
            filter: {
                conditions: [
                    {
                        fields: [
                            {a: {equal: 1}},
                            {b: {equal: 2}},
                            {c: {equal: 3}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                    {
                        fields: [
                            {d: {equal: 4}},
                            {e: {equal: 5}},
                            {f: {equal: 6}},
                            {x: {equal: 'xxxx'}},
                            {y: {equal: 'yyyy'}},
                        ],
                    },
                ],
            },
        };

        manager.set('a', variablesA);
        expect(manager.variables.value).toEqual(variablesA);

        manager.contextualizeChannel('a', contextFields);
        expect(manager.variables.value).toEqual(resultA);

        manager.merge('a', variablesB);
        expect(manager.variables.value).toEqual(resultB);

    });

});
