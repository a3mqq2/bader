ALTER TABLE shipments MODIFY weight VARCHAR(10);

UPDATE shipments
SET weight = CASE weight
    WHEN '½ كيلو' THEN '0.5'
    WHEN '1 كيلو' THEN '1'
    WHEN '2 كيلو' THEN '2'
    WHEN '3 كيلو' THEN '3'
    WHEN '4 كيلو' THEN '4'
    WHEN '5 كيلو' THEN '5'
    ELSE weight
END;
