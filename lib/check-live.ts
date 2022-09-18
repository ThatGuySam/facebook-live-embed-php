interface CheckLiveOptions {
    identifiers: Map<string, string>
}

export interface CheckLive {
    identifiers: Map<string, string>
    checkLive ( name:string, identifier:string ): Promise<any>
    check (): Promise<any>
}

export class CheckLive {
    constructor ( options:CheckLiveOptions ) {
        this.identifiers = options.identifiers
    }

    static async checkLive ( name, identifier ) {
        throw new Error( 'Not implemented on CheckLive' )
    }

    async check () {

        // Run requests in parallel
        // so that timings are as similar as possible
        const results = await Promise.all( Array.from( this.identifiers ).map( ([ name, identifier ]) => {
            return this.checkLive( name, identifier )
        }) )

        return results
    }
}