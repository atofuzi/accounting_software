// 変換するキーを取得
const mapKeysDeep = (data, callback) => {
    if (_.isArray(data)) {
        return data.map(innerData => mapKeysDeep(innerData, callback));
    } else if (_.isObject(data)) {
        return _.mapValues(_.mapKeys(data, callback), val =>
            mapKeysDeep(val, callback)
        );
    } else {
        return data;
    }
};

// キャメルケースへ変換
const mapKeysCamelCase = data =>
    mapKeysDeep(data, (_value, key) => _.camelCase(key));

// スネークケースへ変換 
const mapKeysSnakeCase = data =>
    mapKeysDeep(data, (_value, key) => _.snakeCase(key));

export { mapKeysCamelCase, mapKeysSnakeCase }