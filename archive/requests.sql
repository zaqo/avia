SELECT * FROM `services` 
LEFT JOIN service_nick ON services.id=service_nick.service_id
WHERE isBundle=1;