create table pages (
    url varchar primary key,
    realm varchar not null,
    title varchar not null,
    content text not null,
    metadata text,
    created timestamp without time zone not null default now()
);

create table pages_trace (
    action char(1) not null,
    dbuser text NOT NULL,
    like pages including defaults
);
