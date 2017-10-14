CREATE TABLE message (
  id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT '��������� ����, �������',
  ref_id INTEGER(11) DEFAULT NULL COMMENT 'ID ��������� ��������� (��� ��������� ��������� ������). ��� �������� ������',
  direction VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '����������� ������. ��������: in - ��������, out - ���������',
  update_id INTEGER(11) DEFAULT NULL,
  chat_id INTEGER(11) DEFAULT NULL COMMENT 'chat_id',
  message_id INTEGER(11) DEFAULT NULL COMMENT '$message_id',
  first_name VARCHAR(100) COLLATE utf8_general_ci DEFAULT NULL COMMENT '$first_name',
  last_name VARCHAR(100) COLLATE utf8_general_ci DEFAULT NULL COMMENT '$last_name',
  chat_type VARCHAR(30) COLLATE utf8_general_ci DEFAULT NULL COMMENT '$chat_type',
  date_telegram INTEGER(11) DEFAULT NULL,
  text TEXT COLLATE utf8_general_ci COMMENT '��� ���� ���������',
  insert_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ������� ������ (�������������)',
  PRIMARY KEY (id)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';