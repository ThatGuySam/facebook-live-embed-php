import { assert, expect, test } from 'vitest'

// Edit an assertion and save to see HMR in action

test('Can test', () => {

    try { 
        assert(true)
    } catch (e) {
        expect(e).toBeUndefined()
    }

})