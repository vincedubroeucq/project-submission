'use strict';
const viewPassordCheckbox = document.querySelector('#show-password [type="checkbox"]');
const passInput = document.querySelector('[type="password"]');
if( viewPassordCheckbox ){
    viewPassordCheckbox.addEventListener( 'change', e => {
        if( e.target.checked ){
            passInput.setAttribute( 'type', 'text' );
        } else {
            passInput.setAttribute( 'type', 'password' );
        }
    }, true );
}
