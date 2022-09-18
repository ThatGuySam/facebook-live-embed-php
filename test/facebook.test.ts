import { assert, expect, test } from 'vitest'

import { FacebookCheckLive } from '../lib/facebook'



// Edit an assertion and save to see HMR in action

test( 'Can test', () => {

    try { 
        assert(true)
    } catch (e) {
        expect(e).toBeUndefined()
    }

} )

const liveChannels = new Map([

    // Not Live
    [ 'JordanHJ', 'UCenBYEupFr45ztQdv0Q00Gg' ],
    [ 'HowCommunicationWorks', 'UCQ0v32jT6Uf3FQ5TI4tudMg' ],
    [ 'jawed', 'UC4QobU6STFB0P71PMvOGN5A' ], 
    [ 'Random', 'UC2ARRjsp3TdvVZvj5R9Mhpg' ], 


    // Live
    [ 'Random Live', 'UCyq7r6SK3M-neYZ28iD_Bgg' ], 
    [ 'KPRC Live', 'UCKQECjul8nw1KW_JzfBTP1A' ], 
    [ 'SkyNews Live', 'UCoMdktPbSTixAyNGwb-UYkQ' ], 
    [ 'ABC Live', 'UCBi2mrWuNuyYy4gbM6fU18Q' ], 
    // [ 'WAPO Mobile Live Videos List', 'UCHd62-u_v4DvJ8TCFtpi4GA' ], 



    // [ 'KPRC', 'KPRC2Click2Houston' ], 
    // [ 'KPRC', 'kprc2' ], 
    // [ 'SkyNews Mobile Live Videos List', 'SkyNews' ], 
    // [ 'ABC Mobile Live Videos List', 'ABCNews' ], 

    // [ 'WAPO Mobile Live Videos List', 'WashingtonPost' ],

    // [ 'JordanHJ', 'JordanHJ' ],
    // [ 'HowCommunicationWorks', 'HowCommunicationWorks' ],
])

test( 'Can detect live channels', async () => {

    const liveCheck = new FacebookCheckLive({
        identifiers: liveChannels
    })

    // Run check
    const liveResults = await liveCheck.check()
    
    let previousResponseHeaders:any = null

    // Compare response headers for live channels
    for ( const responseParts of liveResults ) {

        if ( previousResponseHeaders !== null ) {
            expect( responseParts ).toEqual( previousResponseHeaders )
        }

        previousResponseHeaders = responseParts

    }



} )

