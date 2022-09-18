import { CheckLive } from './check-live'

export class FacebookCheckLive extends CheckLive {

    async checkLive ( name, identifier ) {

        // const response = await youTubeAxios.head( url, {
        //     headers: { 'user-agent': pixelUA }
        // } )
        // .catch( ( error ) => {
        //     // console.log( 'error', error )

        //     return error.response
        // } )

        return [ 'works' ]
    }
}