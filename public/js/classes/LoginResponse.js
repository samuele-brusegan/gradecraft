/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class LoginResponse {
    //Attributi: ident, firstName, lastName, showPwdChangeReminder, tokenAP, token, release, expire
    constructor(ident, firstName, lastName, showPwdChangeReminder, tokenAP, token, release, expire) {
        this.ident = ident;
        this.firstName = firstName;
        this.lastName = lastName;
        this.showPwdChangeReminder = showPwdChangeReminder;
        this.tokenAP = tokenAP;
        this.token = token;
        this.release = release;
        this.expire = expire;
    }

}