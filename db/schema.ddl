
create table fgac_rules (
  id bigint(11) not null auto_increment,
  `name` varchar(140) not null,
  `table_name` varchar(140) not  null ,
  plugin varchar (140) not null ,

  primary key (id)
);

create table fgac_acl (
  fgac_id bigint(11) not null ,
  role_id bigint(11) not null ,

  primary key (fgac_id, role_id),
  foreign key (fgac_id) references fgac_rules (id),
  foreign key (role_id) references acl_roles (id)
);