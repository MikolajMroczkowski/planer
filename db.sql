create table if not exists users
(
    id        int auto_increment
        primary key,
    username  text     not null,
    firstname text     not null,
    lastname  text     not null,
    password  text     not null,
    phone     tinytext not null,
    email     text     null,
    isActive  bit      null,
    constraint users_id_uindex
        unique (id)
)
    auto_increment = 21;

create table if not exists activities
(
    id           int auto_increment
        primary key,
    name         text                          null,
    owner        int                           null,
    notifyBySms  bit            default b'0'   null,
    notifyByMail bit            default b'0'   null,
    time         int            default 60     null,
    price        decimal(15, 2) default 100.00 null,
    constraint planned_activities_id_uindex
        unique (id),
    constraint planned_activities_users_id_fk
        foreign key (owner) references users (id)
            on delete cascade
);

create table if not exists activities_terms
(
    id         int auto_increment
        primary key,
    activity   int      null,
    term_start datetime null,
    term_end   datetime null,
    constraint activities_terms_id_uindex
        unique (id),
    constraint activities_terms_activities_id_fk
        foreign key (activity) references activities (id)
);

create table if not exists payu
(
    id         int auto_increment
        primary key,
    user       int  null,
    value      int  null,
    payuid     text null,
    isFinished bit  null,
    constraint payu_id_uindex
        unique (id),
    constraint payu_users_id_fk
        foreign key (user) references users (id)
            on delete cascade
);

create table if not exists users_cost
(
    id          int auto_increment
        primary key,
    user        int            null,
    value       decimal(15, 2) null,
    whatHappend text           null,
    constraint users_cost_id_uindex
        unique (id),
    constraint users_cost_users_id_fk
        foreign key (user) references users (id)
);

create table if not exists users_email_verification
(
    id   int auto_increment
        primary key,
    user int  null,
    code text null,
    constraint user_email_verification_id_uindex
        unique (id),
    constraint users_email_verification_users_id_fk
        foreign key (user) references users (id)
            on delete cascade
)
    auto_increment = 15;

create table if not exists users_lostpass
(
    id   int auto_increment
        primary key,
    user int  null,
    code text null,
    constraint users_lostpass_users_id_fk
        foreign key (user) references users (id)
            on delete cascade
);

create table if not exists users_sessions
(
    id   int auto_increment
        primary key,
    user int  null,
    code text null,
    constraint users_sessions_users_id_fk
        foreign key (user) references users (id)
            on delete cascade
)
    auto_increment = 8;

create table if not exists users_sms_verification
(
    id   int auto_increment
        primary key,
    user int  null,
    code text null,
    constraint users_sms_verification_users_id_fk
        foreign key (user) references users (id)
            on delete cascade
)
    auto_increment = 11;

create table if not exists users_users
(
    id        int auto_increment
        primary key,
    owner     int      not null,
    firstname text     not null,
    lastname  text     not null,
    mail      text     null,
    phone     tinytext null,
    constraint users_users_id_uindex
        unique (id),
    constraint users_users_users_id_fk
        foreign key (owner) references users (id)
            on delete cascade
);

create table if not exists activities_users
(
    id       int auto_increment
        primary key,
    activity int not null,
    user     int null,
    constraint activities_users_id_uindex
        unique (id),
    constraint activities_users_activities_id_fk
        foreign key (activity) references activities (id)
            on delete cascade,
    constraint activities_users_users_users_id_fk
        foreign key (user) references users_users (id)
            on delete cascade
);

