<?php

namespace SimpleSolution\SimpleTradeBundle\Consts;

class Constants
{
    /**
     * NEWS
     */
    const NEWS_PERFORMER = 1;
    const NEWS_CUSTOMER = 2;
    const NEWS_SRO = 4;
    const NEWS_ANONYMOUS = 8;

    /**
     * MAIL
     */
    const MAIL_2USER_REGISTRATION = 1;
    const MAIL_2CONTRACTOR_QUALIFICATION_INCREASE = 2;
    const MAIL_2SRO_QUALIFICATION_INCREASE = 3;
    const MAIL_2USER_REGISTRATION_CONFIRMATION = 4;
    const MAIL_2COMPANY_NEW_NEWS = 5;
    const MAIL_2USER_AUCTION_ADMITTED = 6;
    const MAIL_2USER_AUCTION_REFUSED = 7;
    const MAIL_2USER_PASSWORD_RESTORE = 8;
    const MAIL_2USER_NEW_PASSWORD = 9;
    const MAIL_2COMPANY_NEW_AUCTION = 10;
    const MAIL_2USER_REGISTRATION_AGREEMENT = 11;
    const MAIL_TOTAL = 11;
    const MAIL_2COMPANY_REFUSAL_AUCTION = 12;

    /*
     * REPORTS
     */
    const FINANCE_REPORT = 13;

    /**
     * ROLE
     */
    const ROLE_CUSTOMER = 'CUSTOMER';
    const ROLE_NOT_APPROVED_CUSTOMER = 'NOT_APPROVED_CUSTOMER';
    const ROLE_PERFORMER = 'PERFORMER';
    const ROLE_NOT_APPROVED_PERFORMER = 'NOT_APPROVED_PERFORMER';
    const ROLE_ADMIN = 'ADMIN';
    const ROLE_SRO = 'SRO';
    const ROLE_COMPANY_ADMIN = 'COMPANY_ADMIN';
    const ROLE_MODERATOR = 'MODERATOR';

}