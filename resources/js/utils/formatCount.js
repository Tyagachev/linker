/**
 * Сокращает большие числа
 *
 * @param count
 * @returns {*|string}
 */
export const formatCount = (count) => {
    if (count >= 1000) {
        return (count / 1000).toFixed(1) + 'тыс.';
    }
    return count;
}
