let nextTodoId = 0
export const addTodo = (text) => ({
  type: 'ADD_TODO',
  id: nextTodoId++,
  text
})

export const setVisibilityFilter = (filter) => ({
  type: 'SET_VISIBILITY_FILTER',
  filter
})

export const toggleTodo = (id) => ({
  type: 'TOGGLE_TODO',
  id
})

export const saveTodo = (id, text, saving) => ({
  type: 'SAVE_TODO',
  id,
  text,
  saving:saving
})
export const editTodo = (id, text) => ({
  type: 'EDIT_TODO',
  id,
  text,
  editing:1
})