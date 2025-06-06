import { useState } from "react";

export function Tabs({ defaultValue, children }) {
  const [value, setValue] = useState(defaultValue);
  return (
    <div>
      {children.map(child =>
        child.type.name === "TabsList"
          ? React.cloneElement(child, { value, setValue })
          : child.props.value === value && child
      )}
    </div>
  );
}

export function TabsList({ children, value, setValue }) {
  return (
    <div className="flex gap-2 mb-4">
      {children.map(child =>
        React.cloneElement(child, {
          isActive: child.props.value === value,
          onClick: () => setValue(child.props.value),
        })
      )}
    </div>
  );
}

export function TabsTrigger({ children, isActive, onClick }) {
  return (
    <button
      onClick={onClick}
      className={`px-3 py-1 rounded ${isActive ? "bg-blue-600 text-white" : "bg-gray-200"}`}
    >
      {children}
    </button>
  );
}

export function TabsContent({ children }) {
  return <div>{children}</div>;
}
