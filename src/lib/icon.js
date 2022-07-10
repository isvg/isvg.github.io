import icons from './icons.json'
const hasIcon = (name) => {
    return icons[name] !== undefined
}
const getIcon = (name) => {
    if (hasIcon(name)) {
        return icons[name]
    }
    return undefined
}

export { hasIcon, getIcon }