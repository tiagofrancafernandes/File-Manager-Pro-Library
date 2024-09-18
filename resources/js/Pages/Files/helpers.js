export function mapItem(item) {
    item.type = item.type || item.typeName;
    item.label = item.label || item.name;

    return {
        ...item,
        class: {
            'bg-red-400':
                item.typeName === 'DIR' && item.color === 'red',
            'bg-blue-400':
                item.typeName === 'DIR' && item.color === 'blue',
            'bg-green-400':
                item.typeName === 'DIR' && item.color === 'green'
        }
    };
}
